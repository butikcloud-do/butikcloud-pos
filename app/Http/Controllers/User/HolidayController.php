<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\HolidayOperation;
use App\Traits\RecycleBinManager;

class HolidayController extends Controller
{
    use HolidayOperation, RecycleBinManager;
}
