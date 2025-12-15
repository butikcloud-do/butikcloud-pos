<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\DepartmentOperation;
use App\Traits\RecycleBinManager;

class DepartmentController extends Controller
{
    use DepartmentOperation, RecycleBinManager;
}
