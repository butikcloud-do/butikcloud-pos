<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Traits\ProductOperation;

class ProductController extends Controller
{
    use ProductOperation;

    /**
     * Enhanced product search for API
     * Returns additional data: cost, last purchase, last sale
     * Supports pagination
     */
    public function search()
    {
        try {
            $page = (int) request()->get('page', 1);
            $perPage = (int) request()->get('perPage', 25);
            
            \Log::info('API Enhanced Product Search Started', [
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
                    \Log::error('Error processing API product detail', [
                        'product_detail_id' => $productDetail->id,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            $message[] = "Enhanced product search results";
            
            // Calculate pagination metadata
            $totalLoaded = $offset + count($enhancedProducts);
            $hasMore = $totalLoaded < $totalCount;

            return jsonResponse('product_search', 'success', $message, [
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
            \Log::error('API Enhanced Product Search Error', [
                'error' => $e->getMessage()
            ]);
            
            $message[] = "Error searching products: " . $e->getMessage();
            return jsonResponse('error', 'error', $message);
        }
    }
}
