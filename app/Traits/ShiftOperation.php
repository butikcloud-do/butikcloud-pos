<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

trait ShiftOperation
{
    public function list()
    {
        $user = getParentUser();

        $baseQuery = Shift::whereHas('company', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->searchable(['name', 'company:name'])
            ->with('company')
            ->orderBy('id', getOrderBy())
            ->trashFilter();

        $pageTitle = 'Manage Shift';
        $view      = "Template::user.hrm.shift.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Shift", "A4 landscape");
        }
        $shifts    = $baseQuery->paginate(getPaginate());
        $companies = Company::where('user_id', $user->id)->active()->get();
        return responseManager("shift", $pageTitle, 'success', compact('shifts', 'view', 'pageTitle', 'companies'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate(
            [
                'name'       => 'required|unique:shifts,name,' . $id . ',id,company_id,' . $request->company_id . '|string|max:255',
                'company_id' => 'required|exists:companies,id',
            ],
            [
                'company_id.required' => 'Please select the company',
            ]
        );


        if ($id) {
            $shift = Shift::where('id', $id)->whereHas('company', function ($q) {
                $q->where('user_id', getParentUser()->id);
            })
                ->firstOrFailWithApi('shift');
            $message = "Shift updated successfully";
            $remark  = "shift-updated";
        } else {
            $shift   = new Shift();
            $message = "Shift saved successfully";
            $remark  = "shift-added";
        }

        $shift->name       = $request->name;
        $shift->company_id = $request->company_id;
        $shift->save();

        adminActivity($remark, get_class($shift), $shift->id);
        return responseManager("shift", $message, 'success', compact('shift'));
    }

    public function status($id)
    {
        return Shift::changeStatus($id);
    }
}
