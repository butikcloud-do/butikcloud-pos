<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait HolidayOperation
{
    public function list()
    {
        $user = getParentUser();

        $baseQuery = Holiday::where('user_id', $user->id)->searchable(['title', 'company:name'])->with('company')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Holiday';
        $view      = "Template::user.hrm.holiday.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Holiday", "A4 landscape");
        }

        $holidays  = $baseQuery->paginate(getPaginate());
        $companies = Company::active()->where('user_id', $user->id)->orderBy('name')->get();

        return responseManager("holiday", $pageTitle, 'success', compact('holidays', 'view', 'pageTitle', 'companies'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate(
            [
                'company_id' => 'required|exists:companies,id',
                'title'      => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'nullable|date|after_or_equal:start_date',
            ],
            [
                'company_id.required' => 'Please select a company',
            ]
        );

        $parentUser = getParentUser();

        if ($id) {
            $holiday = Holiday::where('id', $id)->where('user_id', $parentUser->id)->firstOrFailWithApi('holiday');
            $message = "Holiday updated successfully";
            $remark  = "holiday-updated";
        } else {
            $holiday          = new Holiday();
            $message          = "Holiday saved successfully";
            $remark           = "holiday-added";
            $holiday->user_id = $parentUser->id;
        }

        //days count
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);
        $days  = $start->diffInDays($end) + 1;

        $holiday->company_id  = $request->company_id;
        $holiday->title       = $request->title;
        $holiday->start_date  = $request->start_date;
        $holiday->end_date    = $request->end_date;
        $holiday->days        = intval($days);
        $holiday->description = $request->description;
        $holiday->save();

        // Notify Employee
        if ($request->notify) {
            $employees = @$holiday->company->employees;
            foreach ($employees as $employee) {
                notify($employee, 'HOLIDAY', [
                    'title'      => $holiday->title,
                    'start_date' => $holiday->start_date,
                    'end_date'   => $holiday->end_date,
                    'days'       => $holiday->days,
                ]);
            }
        }

        adminActivity($remark, get_class($holiday), $holiday->id);
        return responseManager("holiday", $message, 'success', compact('holiday'));
    }
}
