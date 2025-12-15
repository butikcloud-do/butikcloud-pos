<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Admin;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

trait AdminOperation
{
    public function list()
    {
        $admins    = Admin::with('roles')->latest('id')->get();
        $pageTitle = 'Manage Admin';
        $view      = "admin.admin.list";
        $roles     = Role::where('status', Status::ENABLE)->get();
        return responseManager("admin", $pageTitle, 'success', compact('admins', 'view', 'pageTitle', 'roles'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:admins,email,' . $id,
            'username' => 'required|string|max:255|unique:admins,username,' . $id,
            'password' => $id ? 'nullable' : 'required|string|min:6',
            'roles'    => 'nullable|array',
        ]);

        if ($id) {
            $admin   = Admin::where('id', $id)->firstOrFailWithApi('admin');
            $message = "Admin updated successfully";
            $remark  = "admin-updated";
        } else {
            $admin           = new Admin();
            $admin->password = Hash::make($request->password);
            $message         = "Admin saved successfully";
            $remark          = "admin-added";
        }

        $admin->name     = $request->name;
        $admin->email    = $request->email;
        $admin->username = $request->username;
        $admin->save();

        if ($request->roles && count($request->roles)) {
            if ($admin->id == Status::SUPPER_ADMIN_ID) {
                $roleId = array_unique(array_merge($request->roles, Role::where('id', Status::SUPER_ADMIN_ROLE_ID)->pluck('id')->toArray()));
            } else {
                $roleId = $request->roles;
            }
            $roles = Role::whereIn('id', $roleId)->pluck('name')->toArray();
            $admin->syncRoles($roles);
        }

        adminActivity($remark, get_class($admin), $admin->id);
        return responseManager("admin", $message, 'success', compact('admin'));
    }

    public function status($id)
    {
        return Admin::changeStatus($id);
    }


    public function profileUpdate(Request $request)
    {
        $pageTitle = "Profile Update";
        $user     = auth()->user();

        $request->validate([
            'name'  => 'required|max:40',
            'email' => 'required|email|unique:admins,email,' . $user->id,
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                return responseManager("admin", "Couldn't upload your image", 'error');
            }
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;

        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->save();

        $message = "Profile updated successfully";
        return responseManager("profile_update", $message, 'success', compact('pageTitle', 'user'));
    }


    public function passwordUpdate(Request $request)
    {
        $pageTitle = "Change Password";
        $user     = auth()->user();

        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return responseManager("admin", "Old password is incorrect!", 'error');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $message = "Password updated successfully";
        return responseManager("password_update", $message, 'success', compact('pageTitle', 'admin'));
    }
}
