<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\ExpenseCategoryOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    use ExpenseCategoryOperation, RecycleBinManager;
}
