<?php

namespace App\Traits;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait ExpenseCategoryOperation
{
    public function list()
    {
        $user = getParentUser();

        $baseQuery = ExpenseCategory::where('user_id', $user->id)->searchable(['name'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Expense Category';
        $view      = "Template::user.expense.category.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "ExpenseCategory");
        }

        $expenseCategories = $baseQuery->paginate(getPaginate());
        return responseManager("expenseCategories", $pageTitle, 'success', compact('expenseCategories', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('expense_categories', 'name')->where('user_id', getParentUser()->id)->ignore($id)],
        ]);

        $user = getParentUser();

        if ($id) {
            $expenseCategory = ExpenseCategory::where('id', $id)->where('user_id', $user->id)->firstOrFailWithApi('expense category');
            $message         = "Expense category updated successfully";
            $remark          = "expense-category-updated";
        } else {
            $expenseCategory          = new ExpenseCategory();
            $message                  = "Expense category saved successfully";
            $remark                   = "expense-category-added";
            $expenseCategory->user_id = $user->id;
        }

        $expenseCategory->name = $request->name;
        $expenseCategory->save();

        adminActivity($remark, get_class($expenseCategory), $expenseCategory->id);

        return responseManager("expenseCategory", $message, 'success', compact('expenseCategory'));
    }

    public function status($id)
    {
        return ExpenseCategory::changeStatus($id);
    }
}
