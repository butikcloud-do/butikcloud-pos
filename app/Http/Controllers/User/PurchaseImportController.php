<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Tax;
use App\Models\PaymentType;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\ProductStock;
use App\Models\SupplierPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PurchaseImportController extends Controller
{
    private $suppliers = [];
    private $warehouses = [];
    private $taxes = [];
    private $paymentTypes = [];

    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Exact UI Labels as Headers
        $headers = [
            'Purchase Date',
            'Supplier',
            'Warehouse',
            'Status',
            'Reference No',
            
            // Product fields
            'Product',      // Identifier (SKU/Name)
            'Qty',
            'Base Price',
            'Tax Type',     // Exclusive/Inclusive
            'Tax',          // Tax Name
            'Purchase Price',
            'Profit Margin',
            'Sale Price',
            'Sale Discount Type', // Fixed/Percent
            'Sale Discount',
            
            // Summary fields
            'Purchase Discount Type', // Fixed/Percent
            'Purchase Discount',
            'Shipping Amount',
            
            // Payment fields
            'Paid Amount',
            'Paid Date',
            'Due Date',
            'Payment Type',
            'Payment Note'
        ];

        // Write Headers
        foreach ($headers as $index => $header) {
            $col = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col . '1', $header);
            
            // Style Header
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($col . '1')->getFill()->getStartColor()->setRGB('E0E0E0');
        }

        // Write Sample Data (2 Rows to show structure)
        $sampleData = [
            [
                date('Y-m-d'), 'Sample Supplier', 'Main Warehouse', 'Received', 'REF001',
                'SKU001', '10', '50.00', 'Exclusive', 'GST', '59.00', '20', '70.80', 'Percent', '5',
                'Fixed', '100', '50',
                '500', date('Y-m-d'), date('Y-m-d', strtotime('+7 days')), 'Cash', 'Initial payment'
            ],
            [
                date('Y-m-d'), 'Sample Supplier', 'Main Warehouse', 'Received', 'REF001',
                'SKU002', '5', '100.00', 'Inclusive', 'VAT', '100.00', '15', '115.00', 'Fixed', '10',
                'Fixed', '100', '50',
                '500', date('Y-m-d'), date('Y-m-d', strtotime('+7 days')), 'Cash', 'Initial payment'
            ]
        ];

        $rowNum = 2;
        foreach ($sampleData as $row) {
            foreach ($row as $index => $value) {
                $col = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($col . $rowNum, $value);
            }
            $rowNum++;
        }

        // Auto-size columns
        foreach (range(1, count($headers)) as $colIndex) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set response headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="purchase_stock_import_sample.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = getParentUser();
        $this->loadReferenceData($user);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return jsonResponse('validation_error', 'error', ["Excel file must contain a header row and at least one data row"]);
            }

            // Remove header row
            $headerRow = array_shift($rows);

            // PROCESS DATA
            // Strategy: 
            // 1. Validate Header Consistency (Optional, but we use Row 1 as source of truth)
            // 2. Validate Rows
            // 3. Prepare Data
            // 4. Execute Transaction

            $validationResult = $this->validateAndPrepareData($rows, $user);
            
            if (!$validationResult['valid']) {
                return jsonResponse('validation_error', 'error', $validationResult['errors']);
            }

            $purchaseData = $validationResult['data'];

            // TRANSACTION
            DB::beginTransaction();

            try {
                $purchase = $this->createPurchase($purchaseData, $user);
                
                DB::commit();

                adminActivity("purchase-bulk-import", Purchase::class, $purchase->id, "Imported purchase successfully");
                return jsonResponse('import_success', 'success', ["Purchase imported successfully. Invoice: {$purchase->invoice_number}"]);

            } catch (Exception $e) {
                DB::rollBack();
                adminActivity("purchase-bulk-import", Purchase::class, 0, "Import failed: " . $e->getMessage());
                return jsonResponse('exception', 'error', ["System Error: " . $e->getMessage()]);
            }

        } catch (Exception $e) {
            return jsonResponse('exception', 'error', ["File Error: " . $e->getMessage()]);
        }
    }

    private function loadReferenceData($user)
    {
        $this->suppliers = Supplier::where('user_id', $user->id)->active()->get();
        $this->warehouses = Warehouse::where('user_id', $user->id)->active()->get();
        $this->taxes = Tax::where('user_id', $user->id)->active()->get();
        $this->paymentTypes = PaymentType::where('user_id', $user->id)->active()->get();
    }

    private function findInCollection($collection, $name) {
        $name = strtolower(trim($name));
        return $collection->first(function($item) use ($name) {
            return strtolower($item->name) === $name;
        });
    }

    private function validateAndPrepareData($rows, $user)
    {
        $errors = [];
        $products = [];
        
        // --- 1. EXTRACT HEADER DATA (From First Row) ---
        $firstRow = $rows[0]; // 0-indexed, so this is Row 2 in Excel
        
        // Map columns by index (Based on Sample Order)
        $hData = [
            'purchase_date' => trim($firstRow[0] ?? ''),
            'supplier'      => trim($firstRow[1] ?? ''),
            'warehouse'     => trim($firstRow[2] ?? ''),
            'status'        => trim($firstRow[3] ?? ''),
            'reference_no'  => trim($firstRow[4] ?? ''),
            
            'purchase_discount_type' => trim($firstRow[15] ?? 'Fixed'),
            'purchase_discount'      => trim($firstRow[16] ?? 0),
            'shipping_amount'        => trim($firstRow[17] ?? 0),
            
            'paid_amount'   => trim($firstRow[18] ?? 0),
            'paid_date'     => trim($firstRow[19] ?? ''),
            'due_date'      => trim($firstRow[20] ?? ''),
            'payment_type'  => trim($firstRow[21] ?? ''),
            'payment_note'  => trim($firstRow[22] ?? ''),
        ];

        // --- VALIDATE HEADER FIELDS ---
        
        // Date
        if (empty($hData['purchase_date']) || !strtotime($hData['purchase_date'])) {
            $errors[] = "Purchase Date is required and must be valid (Row 2)";
        }

        // Supplier
        $supplier = null;
        if (empty($hData['supplier'])) {
            $errors[] = "Supplier is required (Row 2)";
        } else {
            $supplier = $this->findInCollection($this->suppliers, $hData['supplier']);
            if (!$supplier) $errors[] = "Supplier '{$hData['supplier']}' not found";
        }

        // Warehouse
        $warehouse = null;
        if (empty($hData['warehouse'])) {
            $errors[] = "Warehouse is required (Row 2)";
        } else {
            $warehouse = $this->findInCollection($this->warehouses, $hData['warehouse']);
            if (!$warehouse) $errors[] = "Warehouse '{$hData['warehouse']}' not found";
        }

        // Status
        $allowedStatuses = ['Received', 'Pending', 'Ordered'];
        $statusMap = [
            'Received' => Status::PURCHASE_RECEIVED,
            'Pending'  => Status::PURCHASE_PENDING,
            'Ordered'  => Status::PURCHASE_ORDERED,
        ];
        if (empty($hData['status']) || !in_array($hData['status'], $allowedStatuses)) {
            $errors[] = "Status must be one of: " . implode(', ', $allowedStatuses);
        }

        // --- 2. PROCESS ROWS (PRODUCTS) ---
        $subtotal = 0;

        foreach ($rows as $index => $row) {
            // Skip completely empty rows
            if (empty(array_filter($row, function($v) { return $v !== null && trim($v) !== ''; }))) continue;

            $rowNum = $index + 2; // Excel row number
            
            // Product Fields
            $pData = [
                'sku'               => trim($row[5] ?? ''),
                'qty'               => trim($row[6] ?? 0),
                'base_price'        => trim($row[7] ?? 0),
                'tax_type'          => trim($row[8] ?? ''),
                'tax_name'          => trim($row[9] ?? ''),
                'purchase_price'    => trim($row[10] ?? 0),
                'profit_margin'     => trim($row[11] ?? 0),
                'sale_price'        => trim($row[12] ?? 0),
                'sale_discount_type'=> trim($row[13] ?? ''),
                'sale_discount'     => trim($row[14] ?? 0),
            ];

            // Validate SKU
            if (empty($pData['sku'])) {
                $errors[] = "Row {$rowNum}: Product identifier (SKU) is required";
                continue;
            }

            // Find Product
            $productDetail = ProductDetail::whereHas('product', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('sku', $pData['sku'])->first();

            if (!$productDetail) {
                $errors[] = "Row {$rowNum}: Product with SKU '{$pData['sku']}' not found";
                continue;
            }

            // Validate Numeric Fields
            if ($pData['qty'] <= 0) $errors[] = "Row {$rowNum}: Qty must be greater than 0";
            if ($pData['base_price'] < 0) $errors[] = "Row {$rowNum}: Base Price cannot be negative";
            if ($pData['purchase_price'] < 0) $errors[] = "Row {$rowNum}: Purchase Price cannot be negative";
            if ($pData['sale_price'] < 0) $errors[] = "Row {$rowNum}: Sale Price cannot be negative";

            // Tax Validation & Calculation
            $taxId = 0;
            $taxType = 0;
            $taxAmount = 0;
            $taxPercentage = 0;
            
            if (!empty($pData['tax_name'])) {
                // Debug Log
                \Illuminate\Support\Facades\Log::info("Purchase Import Tax Lookup: Searching for '{$pData['tax_name']}'");
                \Illuminate\Support\Facades\Log::info("Available Taxes: " . $this->taxes->pluck('name')->implode(', '));

                $tax = $this->findInCollection($this->taxes, $pData['tax_name']);
                if ($tax) {
                    $normTaxType = strtolower(trim($pData['tax_type']));
                    if ($normTaxType === 'inclusive') {
                        $taxType = Status::TAX_TYPE_INCLUSIVE;
                    } elseif ($normTaxType === 'exclusive') {
                        $taxType = Status::TAX_TYPE_EXCLUSIVE;
                    } else {
                        $errors[] = "Row {$rowNum}: Tax Type must be 'Exclusive' or 'Inclusive'.";
                    }

                    $taxId = $tax->id;
                    
                    // Logic from makeProductDetails
                    $taxPercentage = $tax->percentage;
                    $taxAmount = floatval($pData['base_price']) * $taxPercentage / 100;
                } else {
                    $errors[] = "Row {$rowNum}: Tax '{$pData['tax_name']}' not found. Leave valid or empty.";
                }
            }

            // Calculate Purchase Price (Logic from makeProductDetails)
            $basePrice = floatval($pData['base_price']);
            $profitMargin = floatval($pData['profit_margin']);
            
            if ($taxType == Status::TAX_TYPE_EXCLUSIVE) {
                $purchasePrice = $basePrice + $taxAmount;
            } else {
                $purchasePrice = $basePrice;
            }

            // Calculate Sale Price
            $profitAmount = $purchasePrice / 100 * $profitMargin;
            $salePrice = $purchasePrice + $profitAmount;

            // Discount Calculation
            $saleDiscType = strtolower($pData['sale_discount_type']) == 'percent' ? Status::DISCOUNT_PERCENT : Status::DISCOUNT_FIXED;
            $saleDiscVal = floatval($pData['sale_discount']);
            $saleDiscAmount = 0;

            if ($saleDiscType == Status::DISCOUNT_PERCENT && $saleDiscVal > 0) {
                if ($saleDiscVal > 100) $saleDiscVal = 100; // Cap at 100%
                $saleDiscAmount = $salePrice / 100 * $saleDiscVal;
            } else {
                $saleDiscAmount = $saleDiscVal;
            }

            if ($saleDiscAmount >= $salePrice && $salePrice > 0) {
                $errors[] = "Row {$rowNum}: Discount amount cannot be greater than or equal to Sale Price.";
            }

            $finalSalePrice = max(0, $salePrice - $saleDiscAmount);

            // Add calculated fields to product data
            $products[] = [
                'product_detail_id' => $productDetail->id,
                'product_id'        => $productDetail->product_id,
                'qty'               => floatval($pData['qty']),
                
                // Pricing fields
                'base_price'        => $basePrice,
                'tax_id'            => $taxId,
                'tax_type'          => $taxType,
                'tax_percentage'    => $taxPercentage,
                'tax_amount'        => $taxAmount,
                
                'purchase_price'    => $purchasePrice,
                'profit_margin'     => $profitMargin,
                'sale_price'        => $salePrice,
                
                'discount_type'     => $saleDiscType,
                'discount_value'    => $saleDiscVal,
                'discount_amount'   => $saleDiscAmount,
                
                'final_price'       => $finalSalePrice,
                'row_number'        => $rowNum,
            ];

            // Calculate Row Total for Subtotal check (Using Calculated Purchase Price)
            $subtotal += floatval($pData['qty']) * $purchasePrice;
        }

        if (empty($products)) {
            $errors[] = "At least one valid product row is required";
        }

        // --- 3. PURCHASE SUMMARY VALIDATION ---
        
        $discountAmount = 0;
        $discType = strtolower($hData['purchase_discount_type']) == 'percent' ? Status::DISCOUNT_PERCENT : Status::DISCOUNT_FIXED;
        $discVal = floatval($hData['purchase_discount']);

        if ($discVal > 0) {
            if ($discType == Status::DISCOUNT_PERCENT) {
                if ($discVal > 100) $errors[] = "Purchase Discount percent cannot exceed 100%";
                $discountAmount = $subtotal * $discVal / 100;
            } else {
                $discountAmount = $discVal;
            }
        }

        if ($discountAmount > $subtotal) {
            $errors[] = "Purchase Discount amount ({$discountAmount}) cannot be greater than Subtotal ({$subtotal})";
        }

        $total = $subtotal - $discountAmount + floatval($hData['shipping_amount']);
        $paidAmount = floatval($hData['paid_amount']);

        if ($paidAmount > $total) {
            $errors[] = "Paid Amount ({$paidAmount}) cannot be greater than Total ({$total})";
        }

        // Payment Type Validation
        $paymentType = null;
        if ($paidAmount > 0) {
            if (empty($hData['payment_type'])) {
                $errors[] = "Payment Type is required when Paid Amount > 0";
            } else {
                $paymentType = $this->findInCollection($this->paymentTypes, $hData['payment_type']);
                if (!$paymentType) $errors[] = "Payment Type '{$hData['payment_type']}' not found";
            }
            
            if (empty($hData['paid_date']) || !strtotime($hData['paid_date'])) {
                $errors[] = "Paid Date is required when Paid Amount > 0";
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        // Return Processed Data
        return [
            'valid' => true,
            'data' => [
                'header' => [
                    'purchase_date' => $hData['purchase_date'],
                    'supplier_id'   => $supplier->id ?? 0,
                    'warehouse_id'  => $warehouse->id ?? 0,
                    'status'        => $statusMap[$hData['status']] ?? 0,
                    'reference_no'  => $hData['reference_no'],
                    'discount_type' => $discType,
                    'discount_value'=> $discVal,
                    'discount_amount'=> $discountAmount,
                    'shipping_amount'=> floatval($hData['shipping_amount']),
                    'subtotal'      => $subtotal,
                    'total'         => $total,
                    'due_amount'    => max(0, $total - $paidAmount),
                    'due_date'      => $hData['due_date'],
                ],
                'payment' => [
                    'amount'        => $paidAmount,
                    'date'          => $hData['paid_date'],
                    'type_id'       => $paymentType->id ?? null,
                    'note'          => $hData['payment_note'],
                ],
                'products' => $products
            ]
        ];
    }

    private function createPurchase($data, $user)
    {
        $h = $data['header'];
        $p = $data['payment'];
        
        // 1. Create Purchase
        $purchase = new Purchase();
        $purchase->user_id          = $user->id;
        $purchase->purchase_by      = auth()->id();
        $purchase->invoice_number   = $this->generateInvoiceNumber($user);
        $purchase->supplier_id      = $h['supplier_id'];
        $purchase->warehouse_id     = $h['warehouse_id'];
        $purchase->purchase_date    = now()->parse($h['purchase_date'])->format('Y-m-d');
        $purchase->reference_number = $h['reference_no'];
        $purchase->discount_type    = $h['discount_type'];
        $purchase->discount_value   = $h['discount_value'];
        $purchase->discount_amount  = $h['discount_amount'];
        $purchase->shipping_amount  = $h['shipping_amount'];
        $purchase->subtotal         = $h['subtotal'];
        $purchase->total            = $h['total'];
        $purchase->status           = $h['status'];
        $purchase->due_amount       = $h['due_amount'];
        $purchase->due_date         = !empty($h['due_date']) ? now()->parse($h['due_date'])->format('Y-m-d') : null;
        $purchase->save();

        // 2. Process Products
        $purchaseDetails = [];
        $isReceived = $h['status'] == Status::PURCHASE_RECEIVED;

        foreach ($data['products'] as $prod) {
            // Update Product Detail (if received)
            if ($isReceived) {
                $prodDetail = ProductDetail::find($prod['product_detail_id']);
                
                // Update pricing on the product itself
                $prodDetail->update([
                    'base_price'     => $prod['base_price'],
                    'purchase_price' => $prod['purchase_price'],
                    'profit_margin'  => $prod['profit_margin'],
                    'sale_price'     => $prod['sale_price'],
                    'discount_type'  => $prod['discount_type'],
                    'discount_value' => $prod['discount_value'],
                    'tax_id'         => $prod['tax_id'],
                    'tax_type'       => $prod['tax_type'],
                ]);

                // Update Stock
                $this->updateStock($purchase, $prodDetail, $prod['qty']);
            }

            // Prepare Purchase Detail Row
            $purchaseDetails[] = [
                'purchase_id'       => $purchase->id,
                'product_id'        => $prod['product_id'],
                'product_details_id'=> $prod['product_detail_id'],
                'quantity'          => $prod['qty'],
                'base_price'        => $prod['base_price'],
                'tax_id'            => $prod['tax_id'],
                'tax_type'          => $prod['tax_type'],
                'tax_percentage'    => $prod['tax_percentage'],
                'tax_amount'        => $prod['tax_amount'],
                'purchase_price'    => $prod['purchase_price'],
                'profit_margin'     => $prod['profit_margin'],
                'sale_price'        => $prod['sale_price'],
                'discount_type'     => $prod['discount_type'],
                'discount_value'    => $prod['discount_value'],
                'discount_amount'   => $prod['discount_amount'],
                'final_price'       => $prod['final_price'],
            ];
        }
        
        PurchaseDetails::insert($purchaseDetails);

        // 3. Process Payment
        if ($p['amount'] > 0) {
            $payment = new SupplierPayment();
            $payment->purchase_id     = $purchase->id;
            $payment->supplier_id     = $purchase->supplier_id;
            $payment->amount          = $p['amount'];
            $payment->payment_date    = now()->parse($p['date'])->format('Y-m-d');
            $payment->payment_type_id = $p['type_id'];
            $payment->payment_note    = $p['note'] ?: "Paid on Import #" . $purchase->invoice_number;
            $payment->save();

            $trxDetails = "Supplier payment on import #" . $purchase->invoice_number;
            createRegisterTransaction(Status::CASH_REGISTER_TYPE_EXPENSE, $p['amount'], $trxDetails, $p['type_id']);
        }

        return $purchase;
    }

    private function updateStock($purchase, $productDetail, $qty)
    {
        $stock = ProductStock::where('product_details_id', $productDetail->id)
            ->where('product_id', $productDetail->product_id)
            ->where('warehouse_id', $purchase->warehouse_id)
            ->whereHas('product', function ($q) use ($purchase) {
                $q->where('user_id', $purchase->user_id);
            })
            ->first();

        if (!$stock) {
            $stock = new ProductStock();
            $stock->warehouse_id = $purchase->warehouse_id;
            $stock->product_id = $productDetail->product_id;
            $stock->product_details_id = $productDetail->id;
            $stock->stock = 0;
        }

        $stock->stock += $qty;
        $stock->save();
    }

    private function generateInvoiceNumber($user)
    {
        $purchaseId = Purchase::where('user_id', $user->id)->count() + 1;
        $prefix = gs('prefix_setting', $user->id);
        $summationNumber = 1000;
        
        if ($prefix) {
            return $prefix->purchase_invoice_prefix . ($summationNumber + $purchaseId);
        } else {
            return $summationNumber + $purchaseId;
        }
    }
}
