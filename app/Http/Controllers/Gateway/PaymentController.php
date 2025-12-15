<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\PlanPurchase;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Razorpay\Api\Plan;

class PaymentController extends Controller
{



    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {

            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user           = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $methodName = $deposit->methodName();

            $transaction                     = new Transaction();
            $transaction->user_id            = $deposit->user_id;
            $transaction->amount             = $deposit->amount;
            $transaction->post_balance       = $user->balance;
            $transaction->charge             = $deposit->charge;
            $transaction->trx_type           = '+';
            $transaction->details            = 'Deposit Via ' . $methodName;
            $transaction->trx                = $deposit->trx;
            $transaction->remark             = 'deposit';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                'amount' => showAmount($deposit->amount, currencyFormat: false),
                'charge' => showAmount($deposit->charge, currencyFormat: false),
                'rate' => showAmount($deposit->rate, currencyFormat: false),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);

            if ($deposit->plan_id) {

                $plan     = SubscriptionPlan::find($deposit->plan_id);
                $now      = $user->plan_expired_at ? Carbon::parse($user->plan_expired_at) : Carbon::now();
                $expireAt = $deposit->plan_recurring_type == Status::YEARLY ? $now->addYear() : $now->addMonth();

                $planPurchase                       = new PlanPurchase();
                $planPurchase->user_id              = $user->id;
                $planPurchase->amount               = $deposit->amount;
                $planPurchase->subscription_plan_id = $plan->id;
                $planPurchase->recurring_type       = $deposit->plan_recurring_type;
                $planPurchase->expired_at           = $expireAt;
                $planPurchase->save();


                $user->plan_id          = $plan->id;
                $user->plan_expired_at  = $expireAt;
                $user->balance         -= $deposit->amount;
                $user->product_limit    = $plan->product_limit   == Status::UNLIMITED ? Status::UNLIMITED : $user->product_limit + $plan->product_limit;
                $user->user_limit       = $plan->user_limit      == Status::UNLIMITED ? Status::UNLIMITED : $user->user_limit + $plan->user_limit;
                $user->warehouse_limit  = $plan->warehouse_limit == Status::UNLIMITED ? Status::UNLIMITED : $user->warehouse_limit + $plan->warehouse_limit;
                $user->supplier_limit   = $plan->supplier_limit  == Status::UNLIMITED ? Status::UNLIMITED : $user->supplier_limit + $plan->supplier_limit;
                $user->coupon_limit     = $plan->coupon_limit    == Status::UNLIMITED ? Status::UNLIMITED : $user->coupon_limit + $plan->coupon_limit;
                $user->hrm_access       = $plan->hrm_access;
                $user->save();

                $transaction                     = new Transaction();
                $transaction->user_id            = $user->id;
                $transaction->amount             = $deposit->amount;
                $transaction->post_balance       = $user->balance;
                $transaction->trx_type           = '-';
                $transaction->details            = 'Plan Purchased';
                $transaction->trx                = getTrx();
                $transaction->remark             = 'plan_purchase';
                $transaction->save();

                notify($user, 'PLAN_PURCHASE', [
                    'trx'          => $transaction->trx,
                    'plan_name'    => $plan->name,
                    'duration'     => showDateTime($expireAt),
                    'amount'       => showAmount($transaction->amount, currencyFormat: false),
                    'next_billing' => showDateTime($expireAt, 'd M Y'),
                    'post_balance' => showAmount($user->balance),
                ]);
            }
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            if ($data->subscription_plan_id) {
                $pageTitle = 'Confirm Payment';
            } else {
                $pageTitle = 'Deposit Confirm';
            }
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = @$gateway->form->form_data;

        if ($formData) {
            $formProcessor = new FormProcessor();
            $validationRule = $formProcessor->valueValidation($formData);
            $request->validate($validationRule);
            $userData = $formProcessor->processFormData($request, $formData);

            $data->detail = $userData;
        }
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        if ($data->subscription_plan_id) {
            $planPurchase         = PlanPurchase::where('user_id', getParentUser()->id)->find($data->subscription_plan_id);
            $planPurchase->status = Status::PLAN_PENDING;
            $planPurchase->save();

            notify($data->user, 'PLAN_PURCHASE_REQUEST', [
                'method_name'     => $data->gatewayCurrency()->name,
                'method_currency' => $data->method_currency,
                'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
                'amount'          => showAmount($data->amount, currencyFormat: false),
                'charge'          => showAmount($data->charge, currencyFormat: false),
                'rate'            => showAmount($data->rate, currencyFormat: false),
                'trx'             => $data->trx,
            ]);

            $notify[] = ['success', 'Plan purchase request has been taken'];
            return to_route('user.transactions')->withNotify($notify);
        }

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
