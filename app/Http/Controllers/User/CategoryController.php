<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\CategoryOperation;
use App\Traits\RecycleBinManager;

class CategoryController extends Controller
{
    use CategoryOperation, RecycleBinManager;
}
