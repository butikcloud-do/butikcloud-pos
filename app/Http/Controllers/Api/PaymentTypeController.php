<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\PaymentTypeOperation;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    use PaymentTypeOperation;
}
