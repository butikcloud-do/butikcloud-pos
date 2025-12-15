<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\CompanyOperation;
use App\Traits\RecycleBinManager;

class CompanyController extends Controller
{
    use CompanyOperation, RecycleBinManager;
}
