<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\StaffPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

trait StaffManager
{
    public function list()
    {
        $user      = getParentUser();
        $pageTitle = "Staff List";
        $view      = "Template::user.staff.list";

        $baseQuery = User::staff()
            ->where('is_deleted', Status::NO)
            ->where('parent_id', $user->id);

        if (request()->export) {
            return exportData($baseQuery, request()->export, "User");
        }

        $staffs = (clone $baseQuery)
            ->searchable(['firstname', 'lastname', 'email', 'username'])
            ->dateFilter('created_at')
            ->trashFilter()
            ->apiQuery();

        return responseManager("staff", $pageTitle, "success", [
            "pageTitle"   => $pageTitle,
            "view"        => $view,
            "staffs"      => $staffs,
            "profilePath" => getFilePath('userProfile'),
        ]);
    }

    public function save(Request $request)
    {
        $user = getParentUser();



        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'firstname'    => 'required',
            'lastname'     => 'required',
            'email'        => 'required|string|email|unique:users',
            'username'     => 'required|string|unique:users',
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        $oneTimePassword = getNumber(10);

        if (!featureAccessLimitCheck($user->user_limit)) {
            $message = "You have reached the maximum limit of adding users. Please upgrade your plan.";
            return responseManager("subscription_reached", $message, "error");
        }

        $staff                   = new User();
        $staff->firstname        = $request->firstname;
        $staff->lastname         = $request->lastname;
        $staff->username         = $request->username;
        $staff->email            = $request->email;
        $staff->country_code     = $request->country_code;
        $staff->country_name     = @$request->country;
        $staff->dial_code        = $request->mobile_code;
        $staff->mobile           = $request->mobile;
        $staff->city             = $request->city;
        $staff->state            = $request->state;
        $staff->zip              = $request->zip;
        $staff->address          = $request->address;
        $staff->parent_id        = $user->id;
        $staff->password         = Hash::make($oneTimePassword);
        $staff->kv               = Status::KYC_VERIFIED;
        $staff->ev               = Status::VERIFIED;
        $staff->sv               = Status::VERIFIED;
        $staff->tv               = Status::VERIFIED;
        $staff->profile_complete = Status::YES;
        $staff->is_staff         = Status::YES;
        $staff->save();

        notify($staff, 'STAFF_REGISTERED', [
            'user'        => $staff->fullname,
            'parent_user' => $user->username,
            'username'    => $staff->username,
            'email'       => $staff->email,
            'password'    => $oneTimePassword,
            'login_url'   => route('user.login'),
        ]);

        decrementFeature($user, 'user_limit');

        $message = "Staff created successfully";
        return responseManager("staff", $message, "success");
    }

    public function edit($id)
    {
        $user  = getParentUser();
        $staff = User::staff()
            ->where('is_deleted', Status::NO)
            ->where('parent_id', $user->id)
            ->findOrFailWithApi("staff", $id);

        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $pageTitle = "Edit Staff - " . $staff->username;
        $view      = "Template::user.staff.edit";
        return responseManager("staff", $pageTitle, "success", [
            "pageTitle"  => $pageTitle,
            "view"       => $view,
            "staff"      => $staff,
            "countries"  => $countries,
            "mobileCode" => $mobileCode,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user  = getParentUser();
        $staff = User::staff()
            ->where('is_deleted', Status::NO)
            ->where('parent_id', $user->id)
            ->findOrFailWithApi("staff", $id);

        $staff->firstname = $request->firstname;
        $staff->lastname  = $request->lastname;
        $staff->city      = $request->city;
        $staff->state     = $request->state;
        $staff->zip       = $request->zip;
        $staff->address   = $request->address;
        $staff->save();

        $message = "Staff updated successfully";
        return responseManager("staff", $message, "success");
    }

    public function permissions($id)
    {
        $user  = getParentUser();
        $staff = User::staff()
            ->where('is_deleted', Status::NO)
            ->where('parent_id', $user->id)
            ->findOrFailWithApi("staff", $id);

        $permissions         = StaffPermission::get();
        $existingPermissions = $staff->staffPermissions;
        $pageTitle           = "Staff Permissions - " . $staff->fullName;
        $view                = "Template::user.staff.permissions";
        return responseManager("staff", $pageTitle, "success", [
            "pageTitle"           => $pageTitle,
            "view"                => $view,
            "staff"               => $staff,
            "permissions"         => $permissions,
            "existingPermissions" => $existingPermissions,
        ]);
    }

    public function updatePermissions(Request $request, $id)
    {
        $request->validate([
            'permissions'   => "nullable|array|min:1",
            'permissions.*' => "nullable|integer",
        ]);

        $user  = getParentUser();
        $staff = User::staff()
            ->where('is_deleted', Status::NO)
            ->where('parent_id', $user->id)
            ->findOrFailWithApi("staff", $id);

        $permissions = StaffPermission::whereIn('id', $request->permissions ?? [])->pluck('id')->toArray();
        $staff->staffPermissions()->sync($permissions);

        $message = "Staff permissions updated successfully";
        return responseManager("staff", $message, "success");
    }

    public function delete($id)
    {
        $user  = getParentUser();
        $staff = User::staff()
            ->where('is_deleted', Status::NO)
            ->where('parent_id', $user->id)
            ->find($id);

        $staff->is_deleted = Status::YES;
        $staff->save();

        $message = "Staff deleted successfully";
        return responseManager("staff", $message, "success");
    }
}
