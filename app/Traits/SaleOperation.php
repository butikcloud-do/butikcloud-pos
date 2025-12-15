<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function Laravel\Prompts\info;

trait SaleOperation
{
    public function list()
    {
        $pageTitle = "Sale List";
        $view      = "Template::user.sale.list";
        $user      = getParentUser();
        $baseQuery = Sale::where('user_id', $user->id)->latest('id');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Sale");
        }

        $sales = (clone $baseQuery)
            ->with("warehouse", "customer", 'payments.paymentType')
            ->withCount('saleDetails')
            ->dateFilter('sale_date')
            ->trashFilter()
            ->filter(['customer_id'])
            ->withSum('payments', 'amount')
            ->searchable(['customer:name,email,mobile', 'invoice_number', 'warehouse:name'])
            ->paginate(getPaginate());

        $widget                           = [];
        $widget['today_sale']             = (clone $baseQuery)->where('sale_date', now()->format("Y-m-d"))->sum('total');
        $widget['yesterday_sale']         = (clone $baseQuery)->where('sale_date', now()->subDay()->format("Y-m-d"))->sum('total');
        $widget['this_week_sale']         = (clone $baseQuery)->where('sale_date', ">=", now()->startOfWeek()->format("Y-m-d"))->sum('total');
        $widget['this_month_sale']        = (clone $baseQuery)->where('sale_date', ">=", now()->startOfMonth()->format("Y-m-d"))->sum('total');
        $widget['last_7days_week_sale']   = (clone $baseQuery)->where('sale_date', ">=", now()->subDays(7)->format("Y-m-d"))->sum('total');
        $widget['last_30days_month_sale'] = (clone $baseQuery)->where('sale_date', ">=", now()->subDays(30)->format("Y-m-d"))->sum('total');
        $widget['all_sale']               = (clone $baseQuery)->sum('total');
        $widget['last_sale_amount']       = (clone $baseQuery)->orderby('id', 'desc')->first()?->total;

        return responseManager("sale", $pageTitle, 'success', compact('pageTitle', 'sales', 'view', 'widget'), ['paymentMethods']);
    }

    public function add()
    {
        $pageTitle = "New Sale";
        $view      = "Template::user.sale.add";
        extract($this->basicDataForSale());
        return responseManager("add_sale", $pageTitle, 'success', compact('pageTitle', 'warehouses', 'paymentMethods', 'view'));
    }

    public function edit($id)
    {
        $pageTitle = "Edit Sale";
        $user      = getParentUser();

        $sale = Sale::where('user_id', $user->id)->where("id", $id)->with("warehouse", "customer", 'payments')->firstOrFailWithApi("sale");
        $view = "Template::user.sale.edit";
        extract($this->basicDataForSale());

        return responseManager("edit_sale", $pageTitle, 'success', compact('pageTitle', 'warehouses', 'paymentMethods', 'view', 'sale'));
    }

    public function view($id)
    {
        $pageTitle          = "Sale Invoice";
        $user               = getParentUser();
        $view               = "Template::user.sale.view";
        $sale               = Sale::where('user_id', $user->id)->where("id", $id)->with("warehouse", "customer", 'payments', 'saleDetails.productDetail.product')->firstOrFailWithApi("sale");
        $companyInformation = gs('company_information');
        return responseManager("view_sale", $pageTitle, 'success', compact('pageTitle', 'sale', 'view', 'companyInformation'));
    }

    public function pdf($id)
    {
        $pageTitle = "Sale Invoice";
        $parent    = getParentUser();
        $sale      = Sale::where("id", $id)->with("warehouse", "customer", 'payments')->where('user_id', $parent->id)->firstOrFailWithApi("sale");
        $pdf       = Pdf::loadView('Template::user.sale.pdf', compact('sale', 'pageTitle'));
        $fileName  = "Sale Invoice - " . $sale->invoice_number . ".pdf";

        return $pdf->download($fileName);
    }
    public function removeSingleItem($id)
    {
        $user     = getParentUser();
        $saleItem = SaleDetails::where('user_id', $user->id)->where("id", $id)->with("sale")->firstOrFailWithApi("SaleDetails");
        $sale     = $saleItem->sale;

        if (SaleDetails::where('sale_id', $sale->id)->count() <= 1) {
            return responseManager("error", "At least one item is required to perform this action.", 'error');
        }
        //adjust stock
        if ($sale->status == Status::SALE_FINAL) {
            $findStock = ProductStock::where('product_id', $saleItem->product_id)
                ->where('product_details_id', $saleItem->product_details_id)
                ->where('warehouse_id', $sale->warehouse_id)
                ->first();
            $findStock->stock += $saleItem->quantity;
            $findStock->save();
        }
        $saleItem->delete();
        return responseManager("success", "Sale Item deleted successfully.", 'success');
    }

    public function store(Request $request)
    {

        $validator = $this->validation($request);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }


        $subtotal            = 0;
        $saleDetails         = [];
        $productUpdateStock  = [];
        $discountTypePercent = Status::DISCOUNT_PERCENT;

        foreach ($request->sale_details as $requestSaleDetails) {

            extract($this->findProductAndProductDetailsAndProductStock($requestSaleDetails, $request->warehouse_id));

            if (!$findStock) {
                $message[] = "The product $productDetails->sku stock is not available";
                return jsonResponse('stock_not_found', 'error', $message);
            }

            if ($requestSaleDetails['quantity'] > $findStock->stock) {
                $message[] = "The product $productDetails->sku stock available $findStock->stock " . @$product->unit->name;
                return jsonResponse('stock_not_found', 'error', $message);
            }

            //new sale details
            $newSaleDetails = $this->makeSaleDetails($requestSaleDetails, $productDetails, $product);
            $saleDetails[]  = $newSaleDetails;
            $subtotal += $newSaleDetails['subtotal'];

            if ($request->status == Status::SALE_FINAL) {
                $productUpdateStock[] = [
                    'stock' => $findStock->stock - $requestSaleDetails['quantity'],
                    'id'    => $findStock->id,
                ];
            }
        }

        $saleDiscountAmount = $this->calculateSaleDiscount($request, $subtotal);
        $shippingAmount     = $request->shipping_amount ?? 0;

        if ($saleDiscountAmount > $subtotal) {
            $message[] = "Maximum discount amount is " . showAmount($subtotal);
            return jsonResponse('limit', 'error', $message);
        }

        $payingAmount = array_reduce($request->payment, function ($carry, $payment) {
            return $carry + $payment['amount'];
        }, 0);

        $total = $subtotal - $saleDiscountAmount + $shippingAmount;

        if ($total <= 0) {
            $message[] = "The total amount must be greater than 0";
            return jsonResponse('limit', 'error', $message);
        }

        if (getAmount($payingAmount) < getAmount($total)) {
            $message[] = "Minimum paying amount is " . showAmount($total);
            return jsonResponse('limit', 'error', $message);
        }

        $changesAmount = $payingAmount - $total;

        if ($changesAmount > 0 && count($request->payment) >= 2) {
            if (!$request->change_payment_type) {
                $message[] = "The changes payment type filed is required";
                return jsonResponse('validation', 'error', $message);
            }
        }


        DB::beginTransaction();

        $user = getParentUser();

        try {
            $sale                  = new Sale();
            $sale->user_id         = $user->id;
            $sale->sale_by         = auth()->id();
            $sale->invoice_number  = $this->invoiceNumber();
            $sale->sale_date       = $request->sale_date ?? date('Y-m-d');
            $sale->customer_id     = $request->customer_id ?? 1;
            $sale->warehouse_id    = $request->warehouse_id;
            $sale->status          = $request->status ?? Status::SALE_FINAL;
            $sale->is_pos_sale     = $request->is_pos_sale ?? Status::YES;
            $sale->discount_type   = $request->discount_type ?? 0;
            $sale->discount_value  = $request->discount_value ?? 0;
            $sale->discount_amount = $saleDiscountAmount;
            $sale->shipping_amount = $shippingAmount;
            $sale->subtotal        = $subtotal;
            $sale->total           = $total;

            $sale->paying_amount = $payingAmount;
            $sale->note      = $request->note ?? null;
            $sale->coupon_id = $request->coupon_id ?? 0;

            $sale->save();

            $saleDetails = array_map(function ($item) use ($sale) {
                $item['sale_id'] = $sale->id;
                return $item;
            }, $saleDetails);


            SaleDetails::insert($saleDetails);
            ProductStock::upsert($productUpdateStock, ['id']);

            $this->insertSalePayment($request, $sale, $changesAmount);

            if (isApiRequest()) {
                Cart::where('user_id', auth()->id())->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            adminActivity("sale", get_class($sale), 0, "Try the new sale, but failed for: " . $e->getMessage());
            return jsonResponse('exception', 'error', $notify);
        }

        $sale->load('customer', 'saleDetails', 'saleDetails.product', 'saleDetails.productDetail');
        adminActivity("sale-add", get_class($sale), $sale->id);

        $html      = "";
        $message[] = "Sale added successfully";

        if ($request->save_action_type == 'save_and_print') {
            $html = view($request->invoice_type == 'regular' ? 'Template::user.sale.invoice' : 'Template::user.sale.pos_invoice', compact('sale'))->render();
        }

        return jsonResponse('sale', 'success', $message, [
            'html'               => $html,
            'sale'               => $sale->load("warehouse", "customer", 'payments', 'saleDetails.productDetail.product'),
            'companyInformation' => gs('company_information'),
        ]);
    }
    public function update(Request $request, $id = 0)
    {


        $validator = $this->validation($request, $id);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $sale = Sale::where('user_id', getParentUser()->id)->where("id", $id)->firstOrFailWithApi("sale");

        if ($sale->status == Status::SALE_FINAL && $request->status == Status::SALE_QUOTATION) {
            $message[] = "Cannot change sale final to quotation";
            return jsonResponse('not_allowed', 'error', $message);
        }

        $subtotal            = 0;
        $newSaleDetails      = [];
        $updatedSaleDetails  = [];
        $productUpdateStock  = [];
        $discountTypePercent = Status::DISCOUNT_PERCENT;

        foreach ($request->sale_details as $requestSaleDetails) {

            extract($this->findProductAndProductDetailsAndProductStock($requestSaleDetails, $sale->warehouse_id));

            // Calculate the discount amount based on the discount type
            if (array_key_exists('id', $requestSaleDetails)) {
                $saleDetails = SaleDetails::find($requestSaleDetails['id']);
                //convert quotation to fina
                if ($sale->status == Status::SALE_QUOTATION && $request->status == Status::SALE_FINAL) {
                    if ($requestSaleDetails['quantity'] > $findStock->stock) {
                        $message[] = "The product $productDetails->sku stock available $findStock->stock " . @$product->unit->name;
                        return jsonResponse('stock_not_found', 'error', $message);
                    }
                    $productUpdateStock[] = [
                        'stock' => $findStock->stock - $requestSaleDetails['quantity'],
                        'id'    => $findStock->id,
                    ];
                } else {
                    if ($saleDetails->quantity != $requestSaleDetails['quantity']) {
                        if ($requestSaleDetails['quantity'] > $saleDetails->quantity) {
                            $stockRequired = $requestSaleDetails['quantity'] - $saleDetails->quantity;

                            if ($findStock->stock < $stockRequired) {
                                $message[] = "The product $productDetails->sku stock available $findStock->stock " . @$product->unit->name;
                                return jsonResponse('stock_not_found', 'error', $message);
                            }
                            $productUpdateStock[] = [
                                'stock' => $findStock->stock - $stockRequired,
                                'id'    => $findStock->id,
                            ];
                        } else {
                            $productUpdateStock[] = [
                                'stock' => $findStock->stock + ($saleDetails->quantity - $requestSaleDetails['quantity']),
                                'id'    => $findStock->id,
                            ];
                        }
                    }
                }
                $createSaleDetails = array_merge(['id' => $requestSaleDetails['id']], $this->makeSaleDetails($requestSaleDetails, $productDetails, $product));
            } else {

                if ($requestSaleDetails['quantity'] > $findStock->stock) {
                    $message[] = "The product $productDetails->sku stock available $findStock->stock " . @$product->unit->name;
                    return jsonResponse('stock_not_found', 'error', $message);
                }

                $createSaleDetails = $this->makeSaleDetails($requestSaleDetails, $productDetails, $product);
                if ($request->status == Status::SALE_FINAL) {
                    $productUpdateStock[] = [
                        'stock' => $findStock->stock - $requestSaleDetails['quantity'],
                        'id'    => $findStock->id,
                    ];
                }
            }

            $subtotal += $createSaleDetails['subtotal'];
            $newSaleDetails[] = $createSaleDetails;
        }

        $saleDiscountAmount = $this->calculateSaleDiscount($request, $subtotal);
        $shippingAmount     = $request->shipping_amount ?? 0;

        if ($saleDiscountAmount > $subtotal) {
            $message[] = "Maximum discount amount is " . showAmount($subtotal);
            return jsonResponse('limit', 'error', $message);
        }

        $total = $subtotal - $saleDiscountAmount + $shippingAmount;

        $payingAmount = array_reduce($request->payment, function ($carry, $payment) {
            return $carry + $payment['amount'];
        }, 0);

        if ($total <= 0) {
            $message[] = "The total amount must be greater than 0";
            return jsonResponse('limit', 'error', $message);
        }

        if (getAmount($payingAmount) < getAmount($total)) {
            $message[] = "Minimum paying amount is " . showAmount($total);
            return jsonResponse('limit', 'error', $message);
        }

        if (count($request->payment) >= 2 && $payingAmount != $total) {
            $message[] = "The paying amount must be " . showAmount($total);
            return jsonResponse('limit', 'error', $message);
        }

        DB::beginTransaction();

        try {
            $sale->sale_date       = $request->sale_date ?? date('Y-m-d');
            $sale->discount_type   = $request->discount_type ?? 0;
            $sale->discount_value  = $request->discount_value ?? 0;
            $sale->discount_amount = $saleDiscountAmount;
            $sale->shipping_amount = $shippingAmount;
            $sale->subtotal        = $subtotal;
            $sale->total           = $total;
            $sale->note            = $request->note ?? null;
            $sale->status          = $request->status;
            $sale->save();


            $newSaleDetails = array_map(function ($item) use ($sale) {
                if (!array_key_exists('id', $item)) {
                    $item['id'] = null;
                }
                $item['sale_id'] = $sale->id;
                return $item;
            }, $newSaleDetails);

            SaleDetails::upsert($newSaleDetails, ['id']);

            if ($sale->status == Status::SALE_FINAL) {
                ProductStock::upsert($productUpdateStock, ['id']);
            }

            $this->updateSalePaymentAndAdjustBalance($request->payment, $sale);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            adminActivity("sale", get_class($sale), $sale->id, "Try the update sale, but failed for: " . $e->getMessage());
            return jsonResponse('exception', 'error', $notify);
        }

        adminActivity("sale-add", get_class($sale), $sale->id);
        $message[] = "Sale updated successfully";

        $sale->load('customer', 'saleDetails', 'saleDetails.product', 'saleDetails.productDetail');

        if ($request->save_action_type == 'save_and_print') {
            $html = view($request->invoice_type == 'regular' ? 'Template::user.sale.invoice' : 'Template::user.sale.pos_invoice', compact('sale'))->render();
        } else {
            $html = "";
        }

        return jsonResponse('sale', 'success', $message, [
            'html' => $html,
        ]);
    }

    private function validation($request, $id = 0)
    {
        $isRequired         = $id ? 'nullable' : "required";
        $isRequiredOnUpdate = $id ? 'required' : "nullable";

        return Validator::make($request->all(), [
            'customer_id'                      => "$isRequired|exists:customers,id",
            'warehouse_id'                     => "$isRequired|integer|exists:warehouses,id",
            'save_action_type'                 => "required|in:save_and_print,only_save",
            'coupon_id'                        => "nullable|exists:coupons,id",
            'status'                           => ['nullable', Rule::in(Status::SALE_FINAL, Status::SALE_QUOTATION)],
            'is_pos_sale'                      => ['nullable', Rule::in(Status::YES, Status::NO)],

            'discount_type'                    => ['nullable', 'integer', Rule::in(Status::DISCOUNT_PERCENT, Status::DISCOUNT_FIXED)],
            'discount_value'                   => 'nullable|numeric|gte:0',
            'shipping_amount'                  => 'nullable|numeric|gte:0',

            'note'                             => 'nullable|string|max:255',

            'sale_details'                     => "required|array|min:1",
            'sale_details.*.id'                => "nullable|integer|exists:sale_details,id",
            'sale_details.*.product_id'        => "required|integer|exists:products,id",
            'sale_details.*.product_detail_id' => "required|integer|exists:product_details,id",
            'sale_details.*.quantity'          => "required|numeric|gt:0",

            "sale_details.*.discount_type"     => ["nullable", Rule::in(Status::DISCOUNT_PERCENT, Status::DISCOUNT_FIXED)],
            "sale_details.*.discount_value"    => "nullable|numeric|gte:0",

            'payment'                          => "required|array|min:1",
            'payment.*.amount'                 => "required|numeric|gt:0",
            'payment.*.id'                     => "$isRequiredOnUpdate|numeric|gt:0",
            'payment.*.payment_type'           => "$isRequired|integer|exists:payment_types,id",
        ], [
            'payment.*.amount.required'             => 'The paid amount filed is required',
            'payment.*.payment_type.required'       => 'The payment type filed is required',
        ]);
    }

    private function invoiceNumber()
    {
        $parentUser      = getParentUser();
        $prefix          = gs('prefix_setting', $parentUser->id);
        $saleCount       = Sale::where('user_id', $parentUser->id)->count() + 1;
        $summationNumber = $parentUser->id . 1000;

        if ($prefix) {
            return $prefix->sale_invoice_prefix . ($summationNumber + $saleCount);
        } else {
            return $summationNumber + $saleCount;
        }
    }

    private function makeSaleDetails($requestSaleDetails, $productDetails, $product): array
    {
        $discountTypePercent = Status::DISCOUNT_PERCENT;

        // Calculate the discount amount based on the discount type
        $discountAmount = 0;
        $discountType   = @$requestSaleDetails['discount_type'] ?? @$productDetails->discount_type;
        $discountValue  = @$requestSaleDetails['discount_value'] ?? @$productDetails->discount_value;

        if ($discountType == $discountTypePercent && $discountValue > 0) {
            if ($discountValue > 100) {
                $message[] = "Maximum discount is 100%";
                return jsonResponse('limit', 'error', $message);
            }
            $discountAmount = $productDetails->sale_price / 100 * $discountValue;
        } else {
            $discountAmount = $discountValue;
        }

        $unitPrice = $productDetails->sale_price - $productDetails->tax_amount;
        $price     = $productDetails->sale_price - $discountAmount;
        $subtotal  = getAmount($price) * $requestSaleDetails['quantity'];

        return [
            'product_id'         => $product->id,
            'product_details_id' => $productDetails->id,
            'tax_id'             => $productDetails->tax_id,
            'tax_type'           => $productDetails->tax_type,
            'tax_amount'         => $productDetails->tax_amount,
            'tax_percentage'     => $productDetails->tax_percentage,
            'purchase_price'     => $productDetails->purchase_price,
            'discount_type'      => $discountType,
            'discount_value'     => $discountValue,
            'discount_amount'    => $discountAmount,
            'unit_price'         => $unitPrice,
            'sale_price'         => $price,
            'subtotal'           => $subtotal,
            'quantity'           => $requestSaleDetails['quantity'],
            'created_at'         => now(),
            'updated_at'         => now()
        ];
    }

    private function findProductAndProductDetailsAndProductStock($requestSaleDetails, $warehouseId)
    {
        $product        = Product::find($requestSaleDetails['product_id']);
        $productDetails = ProductDetail::find($requestSaleDetails['product_detail_id']);
        $findStock      = ProductStock::where('product_id', $product->id)
            ->where('product_details_id', $productDetails->id)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return compact('product', 'productDetails', 'findStock');
    }

    private function calculateSaleDiscount($request, $subtotal)
    {

        $discountTypePercent = Status::DISCOUNT_PERCENT;
        $saleDiscountAmount  = 0;
        $saleDiscountType    = $request->discount_type ?? 0;
        $saleDiscountValue   = $request->discount_value ?? 0;

        if ($saleDiscountType == $discountTypePercent && $saleDiscountValue > 0) {
            $saleDiscountAmount = $subtotal / 100 * $saleDiscountValue;
        } else {
            $saleDiscountAmount = $saleDiscountValue;
        }

        return $saleDiscountAmount;
    }

    private function basicDataForSale()
    {
        $user = getParentUser();

        return [
            'warehouses'     => Warehouse::where('user_id', $user->id)->active()->get(),
            'paymentMethods' => PaymentType::where('user_id', $user->id)->active()->get(),
        ];
    }

    // Coupon

    public function applyCoupon(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required',
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $couponCode = $request->coupon_code;
        $coupon     = Coupon::where('code', $couponCode)
            ->where('status', Status::YES)
            ->where('user_id', getParentUser()->id)
            ->first();

        if (!$coupon) {
            $message[] = "The coupon code you entered is invalid.";
            return jsonResponse('not_found', 'error', $message);
        }

        $expire = Coupon::where('code', $couponCode)
            ->where('status', Status::YES)
            ->whereDate('start_from', '<=', now())
            ->whereDate('end_at', '>=', now())
            ->where('user_id', getParentUser()->id)
            ->first();

        if (!$expire) {
            $message[] = "This coupon has expired or is not currently active";
            return jsonResponse('expired', 'error', $message);
        }

        if ($request->subtotal < $coupon->minimum_amount) {
            $message[] = 'Minimum spend of ' . showAmount($coupon->minimum_amount) . '';
            return jsonResponse('limit', 'error', $message);
        }

        $totalUses = Sale::where('coupon_id', $coupon->id)->count();
        if ($totalUses >= $coupon->maximum_using_time) {
            $message[] = 'This coupon has reached its usage limit.';
            return jsonResponse('limit', 'error', $message);
        }

        $message[] = 'Coupon applied successfully.';
        return jsonResponse('coupon_applied', 'success', $message, [
            'discount_type' => $coupon->discount_type,
            'amount'        => getAmount($coupon->amount),
            'coupon_id'     => $coupon->id,
        ]);
    }

    public function topSellingProduct()
    {
        $pageTitle = 'Top Selling Product';
        $view      = 'Template::user.sale.top_selling_product';

        $topSellingProducts = SaleDetails::selectRaw('SUM(quantity) as total_quantity,product_details_id')
            ->groupBy('product_details_id')
            ->with('productDetail', 'product')
            ->orderBy('total_quantity', 'desc')
            ->searchable(['product:name', 'productDetail:sku'])
            ->paginate(getPaginate());

        return responseManager("top_selling_product", $pageTitle, 'success', compact('pageTitle', 'topSellingProducts', 'view'));
    }

    private function addBalanceToPaymentAccount($sale, $amount, $paymentTypeId)
    {
        $details = "Sale amount added to the payment account: invoice number#" . $sale->invoice_number;
        createRegisterTransaction(Status::CASH_REGISTER_TYPE_SALE, $amount, $details, $paymentTypeId);
    }

    private function updateSalePaymentAndAdjustBalance($payments, $sale)
    {
        foreach ($payments as $payment) {
            $salePayment = SalePayment::where('id', $payment['id'])->first();

            if (!$salePayment) {
                continue;
            }

            $saleAmount = $salePayment->amount;
            $paidAmount = $payment['amount'];

            if ($saleAmount == $paidAmount) {
                continue;
            }

            $salePayment->amount = $paidAmount;
            $salePayment->date   = $sale->date;
            $salePayment->save();


            //TODO:must be check cash register
            if ($sale->status == Status::SALE_FINAL) {
                $details = "Balance adjustment for the update sale: invoice number#" . $sale->invoice_number;
                if ($paidAmount > $saleAmount) {
                    $amount = $paidAmount - $saleAmount;
                    createRegisterTransaction(Status::CASH_REGISTER_TYPE_SALE, $amount, $details, $salePayment->payment_type);
                } else {
                    $amount = $saleAmount - $paidAmount;
                    createRegisterTransaction(Status::CASH_REGISTER_OTHER_DEBIT, $amount, $details, $salePayment->payment_type);
                }
            }
        }
    }

    private function insertSalePayment($request, $sale, $changesAmount)
    {
        $salePayments      = [];
        $now               = now();
        $totalPaymentTypes = count($request->payment);

        foreach ($request->payment as $payments) {

            if ($totalPaymentTypes == 1 && $changesAmount > 0) {
                $amount = $payments['amount'] - $changesAmount;
            } else {
                $amount = $payments['amount'];
            }

            $trx            = getTrx();
            $salePayments[] = array_merge($payments, [
                'created_at'  => $now,
                'updated_at'  => $now,
                'sale_id'     => $sale->id,
                'customer_id' => $request->customer_id,
                'date'        => $sale->sale_date,
                'trx'         => $trx,
                'amount'      => $amount,
            ]);

            if ($sale->status == Status::SALE_FINAL) {
                $this->addBalanceToPaymentAccount($sale, $amount, $payments['payment_type']);
                if ($totalPaymentTypes >= 2) {
                    $changesPaymentAccountType = @$request->change_payment_type;
                    if ($changesPaymentAccountType && $changesAmount > 0) {
                        $details = "Changes amount return to customer. Sale invoice is: #" . $sale->invoice_number;
                        createRegisterTransaction(Status::CASH_REGISTER_OTHER_DEBIT, $changesAmount, $details, $changesPaymentAccountType);
                    }
                }
            }
        }

        SalePayment::insert($salePayments);
    }
}
