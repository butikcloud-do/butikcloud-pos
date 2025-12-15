<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\RecycleBinManager;
use App\Traits\StaffManager;

class ManageStaffController extends Controller
{
    use StaffManager, RecycleBinManager;

    protected $modelName = "User";

    public function create()
    {
        $pageTitle     = "Add Staff";
        $info          = json_decode(json_encode(getIpInfo()), true);
        $mobileCode    = @implode(',', $info['code']);
        $countries     = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view("Template::user.staff.create", compact('pageTitle', 'countries', 'mobileCode'));
    }
}
