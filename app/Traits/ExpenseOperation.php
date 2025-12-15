<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Expense;
use App\Models\ExpenseCategory;

use App\Models\PaymentType;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

trait ExpenseOperation
{
    public function list()
    {
        $user = getParentUser();

        $baseQuery = Expense::orderBy('id', getOrderBy())->where('user_id', $user->id)->with('paymentType');
        $pageTitle = 'Manage Expense';
        $view      = "Template::user.expense.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Expense");
        }

        $expenses          = (clone $baseQuery)->dateFilter('expense_date')->searchable(['reference_no', 'category:name'])->trashFilter()->paginate(getPaginate());
        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->active()->get();
        $widget            = [];

        $widget['today_expense']             = (clone $baseQuery)->where('expense_date', now()->format("Y-m-d"))->sum('amount');
        $widget['yesterday_expense']         = (clone $baseQuery)->where('expense_date', now()->subDay()->format("Y-m-d"))->sum('amount');
        $widget['this_week_expense']         = (clone $baseQuery)->where('expense_date', ">=", now()->startOfWeek()->format("Y-m-d"))->sum('amount');
        $widget['last_7days_week_expense']   = (clone $baseQuery)->where('expense_date', ">=", now()->subDays(7)->format("Y-m-d"))->sum('amount');
        $widget['this_month_expense']        = (clone $baseQuery)->where('expense_date', ">=", now()->startOfMonth()->format("Y-m-d"))->sum('amount');
        $widget['last_30days_month_expense'] = (clone $baseQuery)->where('expense_date', ">=", now()->subDays(30)->format("Y-m-d"))->sum('amount');
        $widget['all_expense']               = (clone $baseQuery)->sum('amount');
        $widget['last_expense_amount']       = (clone $baseQuery)->orderby('id', 'desc')->first()?->amount;

        $paymentMethods = PaymentType::active()->where('user_id', $user->id)->get();

        return responseManager("expenses", $pageTitle, 'success', compact('expenses', 'view', 'pageTitle', 'expenseCategories', 'widget', 'paymentMethods'), ['expenseCategories', 'paymentMethods']);
    }

    public function save(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';

        $request->validate([
            'expense_date'    => 'required|date',
            'reference_no'    => 'nullable|string',
            'comment'         => 'nullable|string',
            'expense_purpose' => 'required|integer',
            'amount'          => 'required|numeric|gt:0',
            'payment_type'    => "$isRequired|integer|exists:payment_types,id",
            'attachment'      => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf', 'docx'])],
        ]);

        $parentUser     = getParentUser();
        $paymentType    = PaymentType::where('id', $request->payment_type)->where('user_id', $parentUser->id)->first();

        if (!$paymentType) {
            return responseManager("error", "The payment type is not found");
        }

        if ($id) {
            $expense          = Expense::where('id', $id)->where('user_id', $parentUser->id)->firstOrFailWithApi('expense');
            $message          = "Expense updated successfully";
            $remark           = "expense-updated";
            $oldExpenseAmount = $expense->amount;
        } else {
            $expense                     = new Expense();
            $expense->added_by           = auth()->id();
            $message                     = "Expense added successfully";
            $remark                      = "expense-add";
            $expense->user_id            = $parentUser->id;
            $expense->payment_type_id    = $request->payment_type;
            $oldExpenseAmount            = 0;
        }

        $expense->expense_date = $request->expense_date;
        $expense->category_id  = $request->expense_purpose;
        $expense->reference_no = $request->reference_no ?? null;
        $expense->comment      = $request->comment ?? null;
        $expense->amount       = $request->amount;

        if ($request->hasFile('attachment')) {
            $expense->attachment = fileUploader($request->attachment, getFilePath("expense_attachment"));
        }

        $expense->save();

        if (!$id) {
            $details = "The expense amount subtract from the payment account";
            createRegisterTransaction(Status::CASH_REGISTER_TYPE_EXPENSE, $request->amount, $details, $paymentType->id);
        } else {
            if ($oldExpenseAmount != $expense->amount) {
                $details        = "Balance adjustment for the update expense";
                if ($expense->amount > $oldExpenseAmount) {
                    $amount = $expense->amount - $oldExpenseAmount;
                    createRegisterTransaction(Status::CASH_REGISTER_TYPE_EXPENSE, $amount, $details, $paymentType->id);
                } else {
                    $amount = $oldExpenseAmount - $expense->amount;
                    $details = "Balance adjustment for the update expense." . $expense->reference_no ? " expense reference no({$expense->reference_no})" : "";
                    createRegisterTransaction(Status::CASH_REGISTER_OTHER_CREDIT, $amount, $details, $paymentType->id);
                }
            }
        }

        adminActivity($remark, get_class($expense), $expense->id);
        return responseManager("expense", $message, 'success', compact('expense'));
    }

    public function status($id)
    {
        return Expense::changeStatus($id);
    }
}
