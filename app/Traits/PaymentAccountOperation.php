<?php

namespace App\Traits;

use App\Models\PaymentAccount;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait PaymentAccountOperation
{
    public function list()
    {

        $user      = getParentUser();
        $baseQuery = PaymentAccount::where('user_id', $user->id)->searchable(['account_name', 'account_number'])->orderBy('id', getOrderBy())->with('paymentType')->trashFilter();
        $pageTitle = 'Manage Payment Account';
        $view      = "Template::user.payment_account.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "PaymentAccount");
        }

        $paymentAccounts = $baseQuery->paginate(getPaginate());
        $paymentTypes    = PaymentType::where('user_id', $user->id)->active()->orderBy('name')->get();
        
        return responseManager("payment_account", $pageTitle, 'success', compact('paymentAccounts', 'view', 'pageTitle', 'paymentTypes'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'payment_type'   => 'required|integer',
            'account_name'   => 'required',
            'account_number' => 'required',
            'note'           => 'nullable',
            'balance'        => 'nullable|numeric|gte:0',
        ]);


        $parentUser  = getParentUser();
        $paymentType = PaymentType::where('id', $request->payment_type)->where('user_id', $parentUser->id)->first();

        if (!$paymentType) {
            return responseManager("payment_account", "The payment type is not found");
        }

        $accountExistsCheck = PaymentAccount::where('user_id', $parentUser->id)->where('payment_type_id', $request->payment_type)->where('account_number', $request->account_number)->where('id', '!=', $id)->exists();

        if ($accountExistsCheck) {
            $message = "This payment account already exists. Please choose a different one.";
            return responseManager("already_exists", $message);
        }

        if ($id) {
            $paymentAccount = PaymentAccount::where('id', $id)->where('user_id', $parentUser->id)->firstOrFailWithApi('Payment Account');
            $message        = "Payment account updated successfully";
            $remark         = "payment-account-updated";
        } else {
            $paymentAccount          = new PaymentAccount();
            $message                 = "Payment account saved successfully";
            $remark                  = "payment-account-added";
            $paymentAccount->user_id = $parentUser->id;
        }

        $exists = PaymentAccount::where('user_id', $parentUser->id)->where('payment_type_id', $request->payment_type)->where('account_number', $request->account_number)->where('id', '!=', $id)->where('account_name', $request->account_name)->exists();

        if ($exists) {
            return responseManager("payment_account", "Payment account already exists", 'error');
        }

        $paymentAccount->payment_type_id = $request->payment_type;
        $paymentAccount->account_name    = $request->account_name;
        $paymentAccount->account_number  = $request->account_number;
        $paymentAccount->note            = $request->note ?? null;
        $paymentAccount->save();

        if (!$id && $request->balance) {
            createTransaction($paymentAccount, "+", $request->balance, "balance_add", "Initial balance added to the account");
        }

        adminActivity($remark, get_class($paymentAccount), $paymentAccount->id);
        return responseManager("payment_account", $message, 'success', compact('paymentAccount'));
    }

    public function adjustBalance(Request $request, $id)
    {

        $request->validate([
            'trx_type' => 'required|in:+,-',
            'note'     => 'required',
            'amount'   => 'required|numeric|gt:0',
        ]);

        $user = getParentUser();

        $paymentAccount = PaymentAccount::where('user_id', $user->id)->where('id', $id)->firstOrFailWithApi('Payment Account');

        if ($request->trx_type == "+") {
            $trxType = "+";
            $remark  = "balance_add";
            $message = "Balance added successfully";
        } else {
            if ($paymentAccount->balance < $request->amount) {
                return responseManager("payment_account", "Not the enough balance to the payment account.", 'error');
            }

            $trxType = "-";
            $message = "Balance subtract successfully";
            $remark  = "balance_subtract";
        }

        createTransaction($paymentAccount, $trxType, $request->amount, $remark, $request->note);

        return responseManager("payment_account", $message, 'success');
    }

    public function transferBalance(Request $request, $id)
    {
        $request->validate([
            'to_account_id' => 'required|exists:payment_accounts,id',
            'amount'        => 'required|numeric|gt:0',
        ]);

        $user = getParentUser();

        $fromAccount = PaymentAccount::where('user_id', $user->id)->where('id', $id)->firstOrFailWithApi('Payment Account');
        $toAccount   = PaymentAccount::where('user_id', $user->id)->where('id', $request->to_account_id)->firstOrFailWithApi('Payment Account');

        if ($fromAccount->id == $toAccount->id) {
            return responseManager("payment_account", "Cannot transfer to the same account.", 'error');
        }

        if ($fromAccount->balance < $request->amount) {
            return responseManager("payment_account", "Not the enough balance to the from account.", 'error');
        }

        DB::transaction(function () use ($fromAccount, $toAccount, $request) {
            createTransaction($fromAccount, "-", $request->amount, "balance_transfer", "Balance transfer from " . $fromAccount->account_name . " to " . $toAccount->account_name);
            createTransaction($toAccount, "+", $request->amount, "balance_transfer", "Balance transfer from " . $fromAccount->account_name . " to " . $toAccount->account_name);
        });

        return responseManager("payment_account", "Balance transfer successfully", 'success');
    }

    public function status($id)
    {
        return PaymentAccount::changeStatus($id);
    }
}