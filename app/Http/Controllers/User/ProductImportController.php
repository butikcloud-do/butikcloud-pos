<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Tax;
use App\Models\Unit;
use App\Traits\ProductOperation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductImportController extends Controller
{
    use ProductOperation;

    public function index()
    {
        $pageTitle = "Import Products";
        return view('Template::user.product.import', compact('pageTitle'));
    }

    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers matching the exact fields from user/product/create
        $headers = [
            'Product Name',
            'Category',
            'Brand',
            'Unit',
            'Product Type',
            'Product Code',
            'SKU',
            'Base Price',
            'Tax Type',
            'Tax',
            'Purchase Price',
            'Profit Margin %',
            'Sale Price',
            'Discount Type',
            'Discount Value',
            'Alert Quantity',
            'Description'
        ];

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data rows
        $sampleData = [
            [
                'Sample Product 1',
                'Electronics',
                'Samsung',
                'Piece',
                'Static',
                '',  // Auto-generated if empty
                '',  // Auto-generated if empty
                '100.00',
                'Exclusive',
                'VAT 18%',
                '95.00',
                '20',
                '120.00',
                'Percent',
                '10',
                '5',
                'This is a sample product description'
            ],
            [
                'Sample Product 2',
                'Furniture',
                'IKEA',
                'Box',
                'Static',
                '',
                '',
                '500.00',
                '',
                '',
                '450.00',
                '15',
                '575.00',
                'Fixed',
                '25',
                '3',
                'Another sample product'
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ];
        $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

        // Set response headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="product_import_sample.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = getParentUser();

        // Check subscription limit BEFORE processing
        if (!featureAccessLimitCheck($user->product_limit)) {
            $message = "You have reached the maximum limit of adding products. Please upgrade your plan.";
            return responseManager("subscription_reached", $message, "error");
        }

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                $message[] = "Excel file must contain at least a header row and one data row";
                return jsonResponse('validation_error', 'error', $message);
            }

            // Remove header row
            array_shift($rows);

            // Remove empty rows
            $rows = array_filter($rows, function ($row) {
                return !empty(array_filter($row));
            });

            if (empty($rows)) {
                $message[] = "No valid data rows found in the Excel file";
                return jsonResponse('validation_error', 'error', $message);
            }

            $errors = [];
            $validData = [];

            // Validate each row BEFORE starting import
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed

                $validation = $this->validateRow($row, $rowNumber, $user);

                if ($validation['valid']) {
                    $validData[] = $validation['data'];
                } else {
                    $errors = array_merge($errors, $validation['errors']);
                }
            }

            // If ANY errors exist, stop here (no partial imports)
            if (!empty($errors)) {
                return jsonResponse('validation_error', 'error', $errors);
            }

            if (empty($validData)) {
                $message[] = "No valid data found to import";
                return jsonResponse('validation_error', 'error', $message);
            }

            // Check if user has enough limit for all products
            $remainingLimit = $user->product_limit;
            if ($remainingLimit !== -1 && count($validData) > $remainingLimit) {
                $message[] = "Cannot import " . count($validData) . " products. Your plan allows only {$remainingLimit} more products. Please upgrade your plan.";
                return jsonResponse('subscription_limit', 'error', $message);
            }

            // ALL-OR-NOTHING: Start transaction for atomic import
            DB::beginTransaction();

            try {
                $importedCount = 0;
                foreach ($validData as $productData) {
                    $this->createProductFromImport($productData, $user);
                    $importedCount++;
                }

                DB::commit();

                $message[] = "Successfully imported {$importedCount} products";
                adminActivity("product-bulk-import", Product::class, 0, "Imported {$importedCount} products successfully");
                return jsonResponse('import_success', 'success', $message);

            } catch (Exception $e) {
                DB::rollBack();
                // Ensure we capture user-friendly exceptions from createProductFromImport
                $message[] = "Import failed: " . $e->getMessage();
                adminActivity("product-bulk-import", Product::class, 0, "Import failed: " . $e->getMessage());
                return jsonResponse('exception', 'error', $message);
            }

        } catch (Exception $e) {
            $message[] = "Failed to read Excel file: " . $e->getMessage();
            return jsonResponse('exception', 'error', $message);
        }
    }

    private function validateRow($row, $rowNumber, $user)
    {
        $errors = [];

        // Map Excel columns (matching sample file structure)
        $data = [
            'product_name'    => trim($row[0] ?? ''),
            'category_name'   => trim($row[1] ?? ''),
            'brand_name'      => trim($row[2] ?? ''),
            'unit_name'       => trim($row[3] ?? ''),
            'product_type'    => trim($row[4] ?? ''),
            'product_code'    => trim($row[5] ?? ''),
            'sku'             => trim($row[6] ?? ''),
            'base_price'      => trim($row[7] ?? ''),
            'tax_type'        => trim($row[8] ?? ''),
            'tax_name'        => trim($row[9] ?? ''),
            'purchase_price'  => trim($row[10] ?? ''),
            'profit_margin'   => trim($row[11] ?? ''),
            'sale_price'      => trim($row[12] ?? ''),
            'discount_type'   => trim($row[13] ?? ''),
            'discount_value'  => trim($row[14] ?? ''),
            'alert_quantity'  => trim($row[15] ?? ''),
            'description'     => trim($row[16] ?? ''),
        ];

        // Required field validation
        $required = ['product_name', 'category_name', 'brand_name', 'unit_name', 'base_price', 'profit_margin', 'sale_price', 'alert_quantity'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $fieldLabel = ucwords(str_replace('_', ' ', $field));
                $errors[] = "Row {$rowNumber}: {$fieldLabel} is required";
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        // Validate product type
        $productType = Status::PRODUCT_TYPE_STATIC; // Default
        if (!empty($data['product_type'])) {
            if (strtolower($data['product_type']) === 'static') {
                $productType = Status::PRODUCT_TYPE_STATIC;
            } elseif (strtolower($data['product_type']) === 'variable') {
                $errors[] = "Row {$rowNumber}: Variable products are not supported in bulk import. Please use 'Static' only";
            } else {
                $errors[] = "Row {$rowNumber}: Product Type must be 'Static' or 'Variable'";
            }
        }

        // Validate Category (auto-create if doesn't exist)
        $category = Category::where('user_id', $user->id)
            ->where('name', $data['category_name'])
            ->active()
            ->first();

        if (!$category) {
            $category = Category::where('user_id', $user->id)
                ->where('name', $data['category_name'])
                ->first();
            
            if (!$category) {
                $categoryId = 'CREATE:' . $data['category_name'];
            } else {
                $errors[] = "Row {$rowNumber}: Category '{$data['category_name']}' exists but is inactive";
                $categoryId = null;
            }
        } else {
            $categoryId = $category->id;
        }

        // Validate Brand (auto-create if doesn't exist)
        $brand = Brand::where('user_id', $user->id)
            ->where('name', $data['brand_name'])
            ->active()
            ->first();

        if (!$brand) {
            $brand = Brand::where('user_id', $user->id)
                ->where('name', $data['brand_name'])
                ->first();
            
            if (!$brand) {
                $brandId = 'CREATE:' . $data['brand_name'];
            } else {
                $errors[] = "Row {$rowNumber}: Brand '{$data['brand_name']}' exists but is inactive";
                $brandId = null;
            }
        } else {
            $brandId = $brand->id;
        }

        // Validate Unit (auto-create if doesn't exist)
        $unit = Unit::where('user_id', $user->id)
            ->where('name', $data['unit_name'])
            ->active()
            ->first();

        if (!$unit) {
            $unit = Unit::where('user_id', $user->id)
                ->where('name', $data['unit_name'])
                ->first();
            
            if (!$unit) {
                $unitId = 'CREATE:' . $data['unit_name'];
            } else {
                $errors[] = "Row {$rowNumber}: Unit '{$data['unit_name']}' exists but is inactive";
                $unitId = null;
            }
        } else {
            $unitId = $unit->id;
        }

        // Validate numeric fields
        if (!is_numeric($data['base_price']) || $data['base_price'] <= 0) {
            $errors[] = "Row {$rowNumber}: Base Price must be a positive number";
        }

        if (!is_numeric($data['purchase_price']) || $data['purchase_price'] <= 0) {
            $errors[] = "Row {$rowNumber}: Purchase Price must be a positive number";
        }

        if (!is_numeric($data['profit_margin']) || $data['profit_margin'] < 0) {
            $errors[] = "Row {$rowNumber}: Profit Margin must be a non-negative number";
        }

        if (!is_numeric($data['sale_price']) || $data['sale_price'] <= 0) {
            $errors[] = "Row {$rowNumber}: Sale Price must be a positive number";
        }

        if (!is_numeric($data['alert_quantity']) || $data['alert_quantity'] < 0) {
            $errors[] = "Row {$rowNumber}: Alert Quantity must be a non-negative number";
        }

        // Validate discount
        $discountType = null;
        $discountValue = 0;
        if (!empty($data['discount_type'])) {
            if (strtolower($data['discount_type']) === 'percent') {
                $discountType = Status::DISCOUNT_PERCENT;
            } elseif (strtolower($data['discount_type']) === 'fixed') {
                $discountType = Status::DISCOUNT_FIXED;
            } else {
                $errors[] = "Row {$rowNumber}: Discount Type must be 'Percent' or 'Fixed'";
            }

            if (!empty($data['discount_value'])) {
                if (!is_numeric($data['discount_value']) || $data['discount_value'] < 0) {
                    $errors[] = "Row {$rowNumber}: Discount Value must be a non-negative number";
                } else {
                    $discountValue = $data['discount_value'];
                }
            }
        }

        // Validate Tax (optional)
        $taxId = null;
        $taxType = null;
        if (!empty($data['tax_name'])) {
            $tax = Tax::where('user_id', $user->id)
                ->where('name', $data['tax_name'])
                ->active()
                ->first();

            if (!$tax) {
                $errors[] = "Row {$rowNumber}: Tax '{$data['tax_name']}' does not exist. Please create it first or leave empty";
            } else {
                $taxId = $tax->id;
                
                if (!empty($data['tax_type'])) {
                    if (strtolower($data['tax_type']) === 'exclusive') {
                        $taxType = Status::TAX_TYPE_EXCLUSIVE;
                    } elseif (strtolower($data['tax_type']) === 'inclusive') {
                        $taxType = Status::TAX_TYPE_INCLUSIVE;
                    } else {
                        $errors[] = "Row {$rowNumber}: Tax Type must be 'Exclusive' or 'Inclusive'";
                    }
                } else {
                    $taxType = Status::TAX_TYPE_EXCLUSIVE;
                }
            }
        }



        // Validate product code uniqueness
        if (!empty($data['product_code'])) {
            $existingProduct = Product::where('user_id', $user->id)
                ->where('product_code', $data['product_code'])
                ->first();
            if ($existingProduct) {
                $errors[] = "Row {$rowNumber}: Product Code '{$data['product_code']}' already exists";
            }
        }

        // Validate SKU uniqueness
        if (!empty($data['sku'])) {
            $existingDetail = ProductDetail::where('user_id', $user->id)
                ->where('sku', $data['sku'])
                ->first();
            if ($existingDetail) {
                $errors[] = "Row {$rowNumber}: SKU '{$data['sku']}' already exists";
            }
        }

        // Validate product name uniqueness
        $existingProduct = Product::where('user_id', $user->id)
            ->where('name', $data['product_name'])
            ->first();
        if ($existingProduct) {
            $errors[] = "Row {$rowNumber}: Product Name '{$data['product_name']}' already exists";
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return [
            'valid' => true,
            'data'  => [
                'name'           => $data['product_name'],
                'category_id'    => $categoryId,
                'category_name'  => $data['category_name'],
                'brand_id'       => $brandId,
                'brand_name'     => $data['brand_name'],
                'unit_id'        => $unitId,
                'unit_name'      => $data['unit_name'],
                'product_type'   => $productType,
                'product_code'   => $data['product_code'],
                'sku'            => $data['sku'],
                'base_price'     => $data['base_price'],
                'tax_id'         => $taxId,
                'tax_type'       => $taxType,
                'purchase_price' => $data['purchase_price'],
                'profit_margin'  => $data['profit_margin'],
                'sale_price'     => $data['sale_price'],
                'discount_type'  => $discountType,
                'discount_value' => $discountValue,
                'alert_quantity' => $data['alert_quantity'],
                'description'    => $data['description'],
                'row_number'     => $rowNumber, // Pass row number for error reporting
            ]
        ];
    }

    private function createProductFromImport($data, $user)
    {
        // Auto-create Category if needed
        if (is_string($data['category_id']) && str_starts_with($data['category_id'], 'CREATE:')) {
            $category = Category::create([
                'user_id' => $user->id,
                'name'    => $data['category_name'],
                'status'  => Status::ENABLE,
            ]);
            $data['category_id'] = $category->id;
        }

        // Auto-create Brand if needed
        if (is_string($data['brand_id']) && str_starts_with($data['brand_id'], 'CREATE:')) {
            $brand = Brand::create([
                'user_id' => $user->id,
                'name'    => $data['brand_name'],
                'status'  => Status::ENABLE,
            ]);
            $data['brand_id'] = $brand->id;
        }

        // Auto-create Unit if needed
        if (is_string($data['unit_id']) && str_starts_with($data['unit_id'], 'CREATE:')) {
            $unit = Unit::create([
                'user_id'    => $user->id,
                'name'       => $data['unit_name'],
                'short_name' => substr($data['unit_name'], 0, 10), 
                'status'     => Status::ENABLE,
            ]);
            $data['unit_id'] = $unit->id;
        }

        $productCode = !empty($data['product_code']) ? $data['product_code'] : $this->getProductCode();

        $product                = new Product();
        $product->user_id       = $user->id;
        $product->name          = $data['name'];
        $product->product_code  = $productCode;
        $product->product_type  = $data['product_type'];
        $product->category_id   = $data['category_id'];
        $product->unit_id       = $data['unit_id'];
        $product->brand_id      = $data['brand_id'];
        $product->description   = $data['description'] ?? null;

        // --- IMAGE UPLOAD LOGIC (Reuse existing helpers) ---
        // ---------------------------------------------------
        // ---------------------------------------------------

        $product->save();

        $productDetailData = makeProductDetails([
            'base_price'     => $data['base_price'],
            'tax_id'         => $data['tax_id'],
            'tax_type'       => $data['tax_type'],
            'profit_margin'  => $data['profit_margin'],
            'discount_type'  => $data['discount_type'],
            'discount'       => $data['discount_value'],
            'purchase_price' => $data['purchase_price'],
            'sale_price'     => $data['sale_price'],
        ]);

        $sku = !empty($data['sku']) ? $data['sku'] : $this->generateProductSku([], $product, 1);

        ProductDetail::create(array_merge($productDetailData, [
            'product_id'     => $product->id,
            'user_id'        => $user->id,
            'variant_id'     => 0,
            'attribute_id'   => 0,
            'sku'            => $sku,
            'alert_quantity' => $data['alert_quantity'],
            'barcode_html'   => generateBarcodeHtml($sku),
        ]));

        decrementFeature($user, 'product_limit');
        adminActivity("product-add", get_class($product), $product->id);
    }
}

