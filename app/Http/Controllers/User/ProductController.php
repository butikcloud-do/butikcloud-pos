<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\ProductOperation;
use App\Traits\RecycleBinManager;


class ProductController extends Controller
{
    use ProductOperation, RecycleBinManager;

    public function printLabel()
    {
        $pageTitle = "Print Label";
        return view('Template::user.product.label', compact('pageTitle'));
    }
}
