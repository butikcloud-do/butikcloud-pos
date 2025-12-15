<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ExpenseOperation;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use ExpenseOperation;
}
