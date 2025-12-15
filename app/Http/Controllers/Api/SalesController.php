<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\PaymentType;
use App\Models\ProductDetail;
use App\Models\ProductStock;
use App\Models\Warehouse;
use App\Traits\SaleOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    use SaleOperation;


    public function requiredData()
    {
        $categories = Category::active()->get();
        $warehouses = Warehouse::active()->get();
        $message[]  = "Required data for sales";

        return jsonResponse('products', 'success', $message, [
            'categories' => $categories,
            'warehouses' => $warehouses,
        ]);
    }
    public function coupon()
    {
        $coupons   = Coupon::active()->get();
        $message[] = "Coupon List";
        return jsonResponse('coupon', 'success', $message, [
            'coupons' => $coupons,
        ]);
    }
    public function paymentMethod()
    {
        $paymentMethods = PaymentType::active()->get();
        $message[]      = "Payment Method";

        return jsonResponse('payment_method', 'success', $message, [
            'payment_methods' => $paymentMethods,
        ]);
    }

    public function productList()
    {
        $products  = productForSales();
        $message[] = "Product list";

        return jsonResponse('products', 'success', $message, [
            'products' => $products['products'],
            'hasMore'  => $products['hasMore']
        ]);
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'sku'          => 'required|exists:product_details,sku',
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $productDetails = ProductDetail::where('sku', $request->sku)->whereHas('product', function ($q) {
            $q->active();
        })->first();

        if (!$productDetails) {
            $message[] = "The product is not found";
            return jsonResponse('not_found', 'error', $message);
        }

        $productStock = ProductStock::where('product_details_id', $productDetails->id)->where('warehouse_id', $request->warehouse_id)->first();

        if (!$productStock ||  $productStock->stock <= 0) {
            $message[] = "The product $productDetails->sku stock is not available";
            return jsonResponse('stock_not_found', 'error', $message);
        }

        $user = auth()->user();
        $cart  = Cart::where('product_details_id', $productDetails->id)
            ->where('user_id', $user->id)
            ->first();

        if ($cart) {
            $cart->quantity += 1;
            $cart->save();
        } else {
            $cart                     = new Cart();
            $cart->warehouse_id       = $request->warehouse_id;
            $cart->product_details_id = $productDetails->id;
            $cart->quantity           = 1;
            $cart->user_id           = auth()->id();
            $cart->save();
        }

        $message[] = "Product added to cart successfully";
        return jsonResponse('add_to_cart', 'success', $message);
    }
    public function updateCartQuantity(Request $request, $cartId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $cart = Cart::where('id', $cartId)->first();

        if (!$cart) {
            $message[] = "The cart is not found";
            return jsonResponse('not_found', 'error', $message);
        }

        $productStock = ProductStock::where('product_details_id', $cart->product_details_id)->where('warehouse_id', $cart->warehouse_id)->first();
        $quantity     = $request->quantity;

        if (!$productStock || $quantity > $productStock->stock) {
            $message[] = "The stock is not available";
            return jsonResponse('stock_not_found', 'error', $message);
        }

        $cart->quantity = $quantity;
        $cart->save();

        $message[] = "The cart quantity has been updated successfully";
        return jsonResponse('add_to_cart', 'success', $message);
    }

    public function countCart()
    {
        $cartCount = Cart::where('user_id', auth()->id())->count();
        $message[] = "Cart count: " . $cartCount;
        return jsonResponse('cart_count', 'success', $message, [
            'cart_count' => $cartCount
        ]);
    }
    public function removeCart()
    {
        Cart::where('user_id', auth()->id())->delete();
        $message[] = "The cart has been removed";
        return jsonResponse('remove_cart', 'success', $message);
    }
    public function removeSingleCart($cartId)
    {

        $cart = Cart::where('id', $cartId)->where('user_id', auth()->id())->first();

        if (!$cart) {
            $message[] = "The cart is not found";
            return jsonResponse('not_found', 'error', $message);
        }

        $cart->delete();

        $message[] = "The cart has been removed";
        return jsonResponse('remove_cart', 'success', $message);
    }

    public function checkout()
    {
        $carts    = Cart::where('user_id', auth()->id())->with('productDetail.product')->get();
        $products = [];

        foreach ($carts as $cart) {
            $productDetail = $cart->productDetail;
            $products[] = [
                'id'             => $productDetail->id,
                'name'           => $productDetail->product->name,
                'sku'            => $productDetail->sku,
                'product_type'   => $productDetail->product->product_type,
                'image_src'      => $productDetail->product->image_src,
                'attribute_name' => @$productDetail->attribute->name,
                'variant_name'   => @$productDetail->variant->name,
                'unit_name'      => @$productDetail->product->unit->short_name,
                'price'          => $productDetail->final_price,
                'quantity'       => $cart->quantity,
                'original'       => $productDetail,
                'cart_id'        => $cart->id,
                'in_stock'       => ProductStock::where('warehouse_id', $cart->warehouse_id)->where('product_details_id', $cart->product_details_id)->sum('stock')
            ];
        }
        $message[] = "cart to check";

        return jsonResponse('checkout', 'success', $message, [
            'products' => $products,
        ]);
    }
}
