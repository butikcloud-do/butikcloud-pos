<?php

namespace App\Http\Controllers\User\Pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PaymentType;
use App\Models\ProductDetail;
use App\Models\Warehouse;


class PosController extends Controller
{
    public function index()
    {

        $pageTitle    = "Pos";
        $user         = getParentUser();
        $warehouses   = Warehouse::active()->where('user_id', $user->id)->get();
        $paymentTypes = PaymentType::active()->where('user_id', $user->id)->get();

        return view('pos.index', compact('pageTitle', 'warehouses', 'paymentTypes'));
    }

    public function category()
    {
        $user       = getParentUser();
        $categories = Category::where('user_id', $user->id)->get();
        $message[]  = "Category list";

        return jsonResponse('category', 'success', $message, [
            'categories' => $categories
        ]);
    }

    public function brand()
    {
        $user = getParentUser();
        $brands    = Brand::where('user_id', $user->id)->active()->get();
        $message[] = "Brand list";

        return jsonResponse('brand', 'success', $message, [
            'brands' => $brands
        ]);
    }

    public function product()
    {
        $message[] = "Product List";
        extract(productForSales());
        return jsonResponse('product', 'success', $message, [
            'products' => $products,
            'has_more' => $hasMore
        ]);
    }

    public function productPricingDetails($id)
    {
        $user = getParentUser();
        $product = ProductDetail::with('product')->where('user_id', $user->id)->find($id);

        if (!$product) {
            $message[] = "The product not found";
            return jsonResponse('not_found', 'error', $message);
        }

        $message[] = "Product pricing details";
        $html = view('pos.partials.pricing_details', compact('product'))->render();
        return jsonResponse('not_found', 'success', $message, [
            'html' => $html
        ]);
    }
}
