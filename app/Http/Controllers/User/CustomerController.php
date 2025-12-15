<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\CustomerOperation;
use App\Traits\RecycleBinManager;

class CustomerController extends Controller
{
    use CustomerOperation, RecycleBinManager;

    public function lazyLoadingData()
    {
        $user = getParentUser();

        $data = Customer::where('user_id', $user->id)->searchable(['email', 'name', 'mobile'])->orderBy('id', 'asc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'data'    => $data,
            'more'    => $data->hasMorePages(),
        ]);
    }
}
