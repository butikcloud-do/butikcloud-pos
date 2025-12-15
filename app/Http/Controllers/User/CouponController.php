<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\CouponOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use CouponOperation, RecycleBinManager;
}
