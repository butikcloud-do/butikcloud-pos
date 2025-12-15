<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Employee;

use App\Models\PaymentType;
use App\Models\Payroll;
use Illuminate\Http\Request;

trait PayrollOperation
{
    public function list()
    {

        $parentUser = getParentUser();
        $baseQuery = Payroll::where('user_id', $parentUser->id)->searchable(['employee:name', 'employee:phone'])->with('employee')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Payroll';
        $view      = "Template::user.hrm.payroll.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "payroll", "A4 landscape");
        }

        $payrolls  = $baseQuery->paginate(getPaginate());
        $employees = Employee::where('user_id', $parentUser->id)
            ->active()
            ->orderBy('name')
            ->get();

        $paymentMethods = PaymentType::where('user_id', $parentUser->id)->active()->orderBy('name')->get();

        return responseManager("payroll", $pageTitle, 'success', compact('payrolls', 'view', 'pageTitle', 'employees', 'paymentMethods'));
    }

    public function save(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';

        $request->validate(
            [
                'employee_id'       => 'required|exists:employees,id',
                'date'              => 'required|date',
                'amount'            => 'required|numeric|gt:0',
                'payment_method_id' => "$isRequired|exists:payment_types,id"

            ],
            [
                'employee_id.required'       => 'Please select a employee',
                'payment_method_id.required' => 'Please select a payment method'
            ]
        );

        $getParentUser = getParentUser();

        if ($id) {
            $payroll   = Payroll::where('id', $id)->where('user_id', $getParentUser->id)->firstOrFailWithApi('payroll');
            $message   = "Payroll updated successfully";
            $remark    = "payroll-updated";
            $oldAmount = $payroll->amount;
        } else {
            $payroll                    = new Payroll();
            $message                    = "Payroll saved successfully";
            $remark                     = "payroll-added";
            $payroll->payment_method_id = $request->payment_method_id;
            $oldAmount                  = 0;
            $payroll->user_id           = $getParentUser->id;
        }

        $payroll->employee_id = $request->employee_id;
        $payroll->date        = $request->date;
        $payroll->amount      = $request->amount;

        if (!$id) {
            $details        = "The payroll amount subtract from the payment account";
            createRegisterTransaction(Status::CASH_REGISTER_TYPE_EXPENSE, $request->amount, $details, $payroll->payment_method_id);
        } else {
            if ($oldAmount != $payroll->amount) {
                $details        = "Balance adjustment for the update of the payroll amount";

                if ($payroll->amount > $oldAmount) {
                    $amount = $payroll->amount - $oldAmount;
                    createRegisterTransaction(Status::CASH_REGISTER_TYPE_EXPENSE, $amount, $details, $payroll->payment_method_id);
                } else {
                    $amount = $oldAmount - $payroll->amount;
                    createRegisterTransaction(Status::CASH_REGISTER_OTHER_CREDIT, $amount, $details, $payroll->payment_method_id);
                }
            }
        }

        $payroll->save();
        adminActivity($remark, get_class($payroll), $payroll->id);
        return responseManager("payroll", $message, 'success', compact('payroll'));
    }
}
