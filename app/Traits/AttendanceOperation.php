<?php

namespace App\Traits;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait AttendanceOperation
{
    public function list()
    {
        $user = getParentUser();

        $baseQuery = Attendance::where('user_id', $user->id)->searchable(['employee:name', 'company:name'])->with('company')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Attendance';
        $view      = "Template::user.hrm.attendance.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Attendance", "A4 landscape");
        }
        $attendances = $baseQuery->paginate(getPaginate());

        $companies   = Company::with([
            'employees' => function ($q) {
                $q->notOnLeaveToday();
            },
            'shifts',
        ])->where('user_id', $user->id)->active()->orderBy('name')->get();

        return responseManager("attendance", $pageTitle, 'success', compact('attendances', 'view', 'pageTitle', 'companies'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate(
            [
                'company_id'  => 'required|exists:companies,id',
                'employee_id' => 'required|exists:employees,id',
                'shift_id'    => 'required|exists:shifts,id',
                'date'        => 'required|date',
                'check_in'    => 'required',
                'check_out'   => 'required|after:check_in',
            ],
            [
                'company_id.required'  => 'Please select a company',
                'employee_id.required' => 'Please select a employee',
                'shift_id.required'    => 'Please select a shift',
            ]
        );

        // Check
        $parentUser = getParentUser();
        $company    = Company::where('id', $request->company_id)->where('user_id', $parentUser->id)->firstOrFailWithApi('company');
        $employee   = Employee::where('id', $request->employee_id)->where('user_id', $parentUser->id)->firstOrFailWithApi('employee');

        $attendanceQuery = Attendance::where('company_id', $company->id)
            ->where('employee_id', $request->employee_id)
            ->where('date', $request->date);

        if ($id) {
            $attendanceQuery->where('id', '!=', $id);
        }
        if ($attendanceQuery->exists()) {
            return responseManager("attendance", "This employee has already been marked for this date", 'error');
        }
        if ($id) {
            $attendance = Attendance::where('id', $id)->where('user_id', $parentUser->id)->firstOrFailWithApi('attendance');
            $message    = "Attendance updated successfully";
            $remark     = "attendance-updated";
        } else {
            $attendance          = new Attendance();
            $message             = "Attendance saved successfully";
            $remark              = "attendance-added";
            $attendance->user_id = $parentUser->id;
        }

        /// Duration
        $checkIn  = Carbon::createFromFormat('H:i', $request->check_in);
        $checkOut = Carbon::createFromFormat('H:i', $request->check_out);
        if ($checkOut->lessThan($checkIn)) {
            $checkOut->addDay();
        }
        $diff     = $checkIn->diff($checkOut);
        $duration = sprintf('%02d:%02d', $diff->h, $diff->i);

        $attendance->company_id  = $request->company_id;
        $attendance->employee_id = $request->employee_id;
        $attendance->shift_id    = $request->shift_id;
        $attendance->date        = $request->date;
        $attendance->check_in    = $request->check_in;
        $attendance->check_out   = $request->check_out;
        $attendance->duration    = $duration;
        $attendance->save();

        adminActivity($remark, get_class($attendance), $attendance->id);
        return responseManager("attendance", $message, 'success', compact('attendance'));
    }

    public function status($id)
    {
        return Attendance::changeStatus($id);
    }
}
