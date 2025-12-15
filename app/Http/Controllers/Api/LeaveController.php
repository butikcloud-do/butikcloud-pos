<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\LeaveRequestOperation;
use App\Traits\LeaveTypeOperation;

class LeaveController extends Controller
{
    use LeaveRequestOperation,LeaveTypeOperation;
}
