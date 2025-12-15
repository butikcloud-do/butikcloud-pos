<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\AttributeOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    use AttributeOperation, RecycleBinManager;
}
