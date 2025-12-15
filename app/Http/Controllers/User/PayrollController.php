<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\PayrollOperation;
use App\Traits\RecycleBinManager;

class PayrollController extends Controller
{

    use PayrollOperation, RecycleBinManager;
}
