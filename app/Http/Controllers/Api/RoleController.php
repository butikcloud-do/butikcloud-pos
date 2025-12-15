<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffPermission;
use App\Traits\RoleOperation;

class RoleController extends Controller
{
    use RoleOperation;

    public function assignPermission()
    {
        $user        = auth()->user();
        if ($user->parent_id) {
            $permissions = $user->staffPermissions()->pluck('name')->toArray();
        } else {
            $permissions = StaffPermission::pluck('name')->toArray();
        }

        $message[] = "Assign Permission List";

        return jsonResponse('success', 'success', $message, [
            'assign_permissions' => $permissions
        ]);
    }
}
