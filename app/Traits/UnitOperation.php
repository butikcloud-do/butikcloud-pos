<?php

namespace App\Traits;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait UnitOperation
{
    public function list()
    {
        $user      = getParentUser();
        $baseQuery = Unit::where('user_id', $user->id)->searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());
        $pageTitle = 'Manage Unit';
        $view      = "Template::user.unit.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Unit");
        }

        $units = $baseQuery->paginate(getPaginate());

        return responseManager("units", $pageTitle, 'success', compact('units', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
             'name'       => ['required', 'string', 'max:40', Rule::unique('units', 'name')->where('user_id', getParentUser()->id)->whereNull('deleted_at')->ignore($id)],
            'short_name' => ['required', 'string', 'max:40', Rule::unique('units', 'short_name')->where('user_id', getParentUser()->id)->whereNull('deleted_at')->ignore($id)],
        ]);

        $user = getParentUser();

        if ($id) {
            $unit    = Unit::where('id', $id)->where('user_id', $user->id)->firstOrFailWithApi('unit');
            $message = "Unit updated successfully";
            $remark  = "unit-updated";
        } else {
            $unit          = new Unit();
            $message       = "Unit saved successfully";
            $remark        = "unit-updated";
            $unit->user_id = $user->id;
        }

        $unit->name       = $request->name;
        $unit->short_name = $request->short_name;
        $unit->save();

        adminActivity($remark, get_class($unit), $unit->id);
        return responseManager("unit", $message, 'success', compact('unit'));
    }

    public function status($id)
    {
        return Unit::changeStatus($id);
    }
}
