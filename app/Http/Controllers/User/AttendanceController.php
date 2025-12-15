<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\AttendanceOperation;
use App\Traits\RecycleBinManager;

class AttendanceController extends Controller
{
    use AttendanceOperation, RecycleBinManager;
}
