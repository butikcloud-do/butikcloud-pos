<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\RecycleBinManager;
use App\Traits\UnitOperation;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use UnitOperation,RecycleBinManager;
}
