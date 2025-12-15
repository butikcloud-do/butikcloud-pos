<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\CashRegister as CashRegisterModel;
use App\Models\CashRegisterTransaction;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use function Symfony\Component\Clock\now;

trait CashRegister
{
    public function dashboard()
    {
        $pageTitle    = 'Create Cash Register';
        $cashRegister = CashRegisterModel::where('user_id', auth()->id())->whereNull('closing_time')->first();  //need active register not has closing time
        $user         = auth()->user();
        return view("Template::user.cash_register.create", compact('pageTitle', 'cashRegister', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'starting_amount' => 'required|numeric|gte:0',
            'starting_note' => 'nullable|max:255'
        ]);

        $user = auth()->user();

        if (CashRegisterModel::where('user_id', $user->id)->whereNull('closing_time')->exists()) {
            return responseManager('error', 'You already have an active cash register', 'error');
        }

        $cashRegister                  = new CashRegisterModel();
        $cashRegister->user_id         = $user->id;
        $cashRegister->parent_id       = getParentUser()->id;
        $cashRegister->starting_amount = $request->starting_amount;
        $cashRegister->starting_note   = $request->starting_note ?? null;
        $cashRegister->starting_time   = now();
        $cashRegister->save();


        return responseManager('error', 'Cash Register Created Successfully', 'success');
    }
    public function close(Request $request)
    {
        $request->validate([
            'closing_amount' => 'required|numeric|gte:0',
            'closing_note'   => 'nullable|max:255'
        ]);

        $user         = auth()->user();
        $cashRegister = CashRegisterModel::where('user_id', $user->id)->whereNull('closing_time')->first();

        if (!$cashRegister) {
            return responseManager('error', 'You do not have an active cash register', 'error');
        }

        $cashRegister->closing_amount = $request->closing_amount;
        $cashRegister->closing_note   = $request->closing_note ?? null;
        $cashRegister->closing_time   = now();
        $cashRegister->save();


        return responseManager('error', 'Cash Register Created Successfully', 'success');
    }
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'amount'       => 'required|numeric|gt:0',
            'action_type'  => ['required', Rule::in(Status::CASH_REGISTER_TYPE_EXPENSE, Status::CASH_REGISTER_TYPE_SALE, Status::CASH_REGISTER_OTHER_CREDIT, Status::CASH_REGISTER_OTHER_DEBIT)],
            'payment_type' => 'required|integer',
            'reason'       => 'required',
        ]);

        $user          = auth()->user();
        $getParentUser = getParentUser();
        $cashRegister  = CashRegisterModel::where('user_id', $user->id)->whereNull('closing_time')->first();

        if (!$cashRegister) {
            return responseManager('error', 'You do not have an active cash register', 'error');
        }

        $paymentType = PaymentType::active()->where('user_id', $getParentUser->id)->where('id', $request->payment_type)->first();

        if (!$paymentType) {
            return responseManager('error', 'Invalid payment type', 'error');
        }

        createRegisterTransaction($request->action_type, $request->amount, $request->reason, $paymentType->id);

        return responseManager('error', 'Cash Register Created Successfully', 'success');
    }



    public function report()
    {
        $pageTitle     = 'Cash Register Reports';
        $cashRegisters = CashRegisterModel::where('parent_id', getParentUser()->id)
            ->filter(['user_id'])
            ->dateFilter()
            ->orderBy('id', getOrderBy())
            ->paginate(getPaginate());
        $view          = "Template::user.cash_register.report";
        return responseManager("cash_registers", $pageTitle, 'success', compact('cashRegisters', 'view', 'pageTitle'));
    }
    public function cashRegisterAccountWiseDetails($cashRegisterId, $accountId, $type)
    {
        $parentUser   = getParentUser();

        $cashRegister = CashRegisterModel::where('parent_id', $parentUser->id)
            ->where('id', $cashRegisterId)
            ->firstOrFailWithApi("CashRegisterModel");

        $paymentType = PaymentType::active()
            ->where('user_id', $parentUser->id)
            ->where('id', $accountId)
            ->firstOrFailWithApi("PaymentType");

        $transactions = CashRegisterTransaction::where('cash_register_id', $cashRegister->id)
            ->where('payment_type_id', $paymentType->id)
            ->where('trx_type', $type)
            ->searchable(['details'])
            ->orderBy('id', getOrderBy())
            ->paginate(getPaginate());

        $view      = "Template::user.cash_register.cash_register_details";
        $pageTitle = 'Cash Register Details';

        return responseManager("cash_registers_details", $pageTitle, 'success', compact('transactions', 'view', 'pageTitle'));
    }

    public function reportDetails($id)
    {
        $cashRegister = CashRegisterModel::where('parent_id', getParentUser()->id)->where('id', $id)->first();

        if (!$cashRegister) {
            return responseManager('error', 'Cash Register not found', 'error');
        }

        $paymentTypes = paymentDetailsForCashRegister($cashRegister);

        $view = view("Template::user.cash_register.report_details", compact('cashRegister', 'paymentTypes'))->render();

        return jsonResponse('cash_register', 'success', ["The cash register details"], [
            'cash_register' => $cashRegister,
            'html'          => $view
        ]);
    }
}
