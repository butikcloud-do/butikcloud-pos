<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Traits\SaleOperation;


class SaleController extends Controller
{

    use SaleOperation;

    public function print($id)
    {

        $sale = Sale::where('user_id', getParentUser()->id)
            ->withSum('payments', 'amount')
            ->where("id", $id)
            ->with("warehouse", "customer")
            ->first();

        if (!$sale) {
            $message[] = "The sale is not found";
            return jsonResponse('not_found', 'error', $message);
        }
        $view      = "Template::user.sale.invoice";
        $message[] = "Print Invoice";

        if (request()->invoice_type == 'pos') {
            $view = "Template::user.sale.pos_invoice";
        }

        return jsonResponse('print', 'success', $message, [
            'html' => view($view, compact('sale'))->render()
        ]);
    }

    public function quickView($id)
    {
        try {
            \Log::info('Quick View Requested', ['product_detail_id' => $id, 'user_id' => getParentUser()->id]);
            $user = getParentUser();
            $productDetail = \App\Models\ProductDetail::where('user_id', $user->id)
                ->where('id', $id)
                ->with(['product', 'product.unit', 'attribute', 'variant'])
                ->withSum(['productStock' => function ($q) {
                    if (request()->warehouse_id) {
                        $q->where('warehouse_id', request()->warehouse_id);
                    }
                }], 'stock')
                ->first();

            if (!$productDetail) {
                return jsonResponse('error', 'error', ['Product not found']);
            }

            $taxes = \App\Models\Tax::where('user_id', $user->id)->active()->get();

            return jsonResponse('success', 'success', ['Product info retrieved'], [
                'product' => [
                    'id' => $productDetail->id,
                    'name' => $productDetail->product->name,
                    'sku' => $productDetail->sku,
                    'in_stock' => $productDetail->product_stock_sum_stock ?? 0,
                    'unit_name' => @$productDetail->product->unit->short_name,
                    'sale_price' => getAmount($productDetail->sale_price),
                    'tax_id' => $productDetail->tax_id,
                    'tax_type' => $productDetail->tax_type,
                    'discount_type' => $productDetail->discount_type,
                    'discount_value' => getAmount($productDetail->discount_value),
                ],
                'taxes' => $taxes
            ]);
        } catch (\Exception $e) {
            \Log::error('Quick View Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return jsonResponse('error', 'error', [$e->getMessage()]);
        }
    }

    /**
     * Enhanced product search for sale/add page only
     * Returns additional data: cost, last purchase, last sale
     * Supports pagination for infinite scroll
     */
    public function searchProductsEnhanced()
    {
        try {
            $page = (int) request()->get('page', 1);
            $perPage = 25;
            
            \Log::info('Enhanced Product Search Started', [
                'search' => request()->search,
                'warehouse_id' => request()->warehouse_id,
                'page' => $page,
                'user_id' => getParentUser()->id
            ]);

            $search      = request()->search;
            $search      = "%$search%";
            $user        = getParentUser();
            $warehouseId = request()->warehouse_id;

            // Base search query
            $searchQuery = \App\Models\ProductDetail::where('user_id', $user->id)
                ->where('sku', request()->search);
            $exactMatch  = true;

            if (!(clone $searchQuery)->count()) {
                $searchQuery->orWhereHas('product', function ($q) use ($search, $user) {
                    $q->where('user_id', $user->id)->where(function ($productQuery) use ($search) {
                        $productQuery->where('sku', "like", $search)
                            ->orWhere('product_code', "like", $search)
                            ->orWhere('name', "like", $search);
                    });
                });
                $exactMatch = false;
            }

            // Add stock for selected warehouse
            if ($warehouseId) {
                $searchQuery->withSum(['productStock' => function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
                }], 'stock');
            }

            // Get total count for pagination
            $totalCount = (clone $searchQuery)->count();
            
            // Get paginated product details
            $offset = ($page - 1) * $perPage;
            $productDetails = $searchQuery->with([
                'product',
                'attribute',
                'variant',
            ])
            ->skip($offset)
            ->take($perPage)
            ->get();

            \Log::info('Product Details Retrieved', [
                'count' => $productDetails->count(),
                'total' => $totalCount,
                'page' => $page
            ]);

            // Enhance each product with last purchase and last sale data
            $enhancedProducts = [];
            
            foreach ($productDetails as $productDetail) {
                try {
                    // Get last purchase for this product detail
                    $lastPurchase = \App\Models\PurchaseDetails::where('product_details_id', $productDetail->id)
                        ->whereHas('purchase', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->with('purchase')
                        ->first();

                    // Get last sale for this product detail
                    $lastSale = \App\Models\SaleDetails::where('product_details_id', $productDetail->id)
                        ->whereHas('sale', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->with('sale')
                        ->first();

                    // Format the product data
                    $formattedProduct = formattedProductDetails(collect([$productDetail]))[0];
                    
                    // Add enhanced data
                    $formattedProduct['cost_price'] = getAmount($productDetail->purchase_price ?? 0);
                    $formattedProduct['selling_price'] = getAmount($productDetail->sale_price ?? 0);
                    
                    // Last purchase info
                    if ($lastPurchase && $lastPurchase->purchase) {
                        $formattedProduct['last_purchase_date'] = $lastPurchase->purchase->purchase_date ?? 'N/A';
                        $formattedProduct['last_purchase_price'] = getAmount($lastPurchase->purchase_price ?? 0);
                    } else {
                        $formattedProduct['last_purchase_date'] = 'N/A';
                        $formattedProduct['last_purchase_price'] = 'N/A';
                    }

                    // Last sale info
                    if ($lastSale && $lastSale->sale) {
                        $formattedProduct['last_sale_date'] = $lastSale->sale->sale_date ?? 'N/A';
                        $formattedProduct['last_sale_price'] = getAmount($lastSale->sale_price ?? 0);
                    } else {
                        $formattedProduct['last_sale_date'] = 'N/A';
                        $formattedProduct['last_sale_price'] = 'N/A';
                    }

                    $enhancedProducts[] = $formattedProduct;
                } catch (\Exception $e) {
                    \Log::error('Error processing product detail', [
                        'product_detail_id' => $productDetail->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue with next product
                    continue;
                }
            }

            \Log::info('Enhanced Products Prepared', [
                'count' => count($enhancedProducts)
            ]);

            $message[] = "Enhanced product search results";
            
            // Calculate pagination metadata
            $totalLoaded = $offset + count($enhancedProducts);
            $hasMore = $totalLoaded < $totalCount;

            return jsonResponse('product_search_enhanced', 'success', $message, [
                'products'    => $enhancedProducts,
                'exact_match' => $exactMatch,
                'pagination'  => [
                    'current_page' => $page,
                    'per_page'     => $perPage,
                    'has_more'     => $hasMore,
                    'total_loaded' => $totalLoaded,
                    'total_count'  => $totalCount,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Enhanced Product Search Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $message[] = "Error searching products: " . $e->getMessage();
            return jsonResponse('error', 'error', $message);
        }
    }
}
