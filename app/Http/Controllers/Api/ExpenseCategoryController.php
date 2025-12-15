<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ExpenseCategoryOperation;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    use ExpenseCategoryOperation;
}
