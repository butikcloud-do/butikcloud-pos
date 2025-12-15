<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Traits\RecycleBinManager;
use App\Traits\SupplierOperation;

class SupplierController extends Controller
{
    use SupplierOperation, RecycleBinManager;

    public function lazyLoadingData()
    {
        $user     = getParentUser();

        $data = Supplier::where('user_id', $user->id)->searchable(['email', 'name'])->orderBy('id', 'desc')->paginate(getPaginate());

        return response()->json([
            'success' => true,
            'data'    => $data,
            'more'    => $data->hasMorePages(),
        ]);
    }
}
