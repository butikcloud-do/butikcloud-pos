<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\EmployeeOperation;
use App\Traits\RecycleBinManager;

class EmployeeController extends Controller
{
    use EmployeeOperation, RecycleBinManager;
}
