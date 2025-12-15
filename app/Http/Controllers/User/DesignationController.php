<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\DesignationOperation;
use App\Traits\RecycleBinManager;

class DesignationController extends Controller
{
    use DesignationOperation, RecycleBinManager;
}
