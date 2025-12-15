<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\PaymentAccountOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class PaymentAccountController extends Controller
{
    use PaymentAccountOperation,RecycleBinManager;


}
