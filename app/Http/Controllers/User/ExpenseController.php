<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\ExpenseOperation;
use App\Traits\RecycleBinManager;

class ExpenseController extends Controller
{
    use ExpenseOperation, RecycleBinManager;
}
