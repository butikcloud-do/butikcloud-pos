<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\PlanPurchase;
use App\Models\SubscriptionPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanSubscribeController extends Controller
{
    public function index()
    {
        $user               = getParentUser();
        $pageTitle          = "Subscription Plans";
        $subscriptionPlans  = SubscriptionPlan::searchable(['name'])->orderBy('id', 'desc')->paginate(getPaginate());
        $subscriptions      = PlanPurchase::where('user_id', $user->id)->filter(['payment_method'])->orderBy('id', 'desc')->with('gateway')->paginate(getPaginate());
        $plan               = @$user->plan;
        $activeSubscription = PlanPurchase::where('user_id', $user->id)->latest('id')->where('subscription_plan_id', $user->plan_id)->first();

        return view('Template::user.subscription.index', compact('pageTitle', 'subscriptionPlans', 'subscriptions', 'activeSubscription', 'plan', 'user'));
    }

    public function planPurchase(Request $request, $id)
    {
        $request->validate([
            'recurring_type' => ['required', Rule::in([Status::MONTHLY, Status::YEARLY])],
        ]);

        $plan   = SubscriptionPlan::active()->findOrFail($id);
        $amount = getPurchasePrice($plan, $request->recurring_type);
        $user   = getParentUser();

        if ($amount <= 0) {
            return $this->manageFreePlanPurchase($user, $plan);
        } else {

            $request->validate([
                'gateway'        => 'required',
                'currency'       => 'required',
            ], [
                'currency.required' => 'The payment method field is required.',
            ]);

            $gate = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

            if (!$gate) {
                $notify[] = ['error', 'Invalid gateway'];
                return back()->withNotify($notify);
            }

            $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
            $payable     = $amount + $charge;
            $finalAmount = $payable * $gate->rate;

            $data                  = new Deposit();
            $data->user_id         = $user->id;
            $data->plan_id         = $plan->id;
            $data->method_code     = $gate->method_code;
            $data->method_currency = strtoupper($gate->currency);
            $data->amount          = $amount;
            $data->charge          = $charge;
            $data->rate            = $gate->rate;
            $data->final_amount    = $finalAmount;
            $data->btc_amount      = 0;
            $data->btc_wallet      = "";
            $data->trx             = getTrx();
            $data->success_url     = route('user.home');
            $data->failed_url      = urlPath('user.deposit.history');
            $data->save();

            session()->put('Track', $data->trx);
            return to_route('user.deposit.confirm');
        }
    }


    public function invoice($subscriptionId)
    {
        $pageTitle    = "Subscription Invoice";
        $user         = getParentUser();
        $subscription = PlanPurchase::where('user_id', $user->id)->with(['subscriptionPlan', 'user'])->findOrFail($subscriptionId);
        return view("Template::user.subscription.invoice", compact('pageTitle', 'subscription'));
    }

    public function downloadInvoice($subscriptionId)
    {
        $pageTitle    = "Download Invoice";
        $user         = getParentUser();
        $subscription = PlanPurchase::where('user_id', $user->id)->with(['subscriptionPlan', 'user'])->findOrFail($subscriptionId);
        $pdf          = Pdf::loadView('Template::user.subscription.print-invoice', compact('subscription', 'pageTitle'));
        $fileName     = 'invoice.pdf';

        return $pdf->stream($fileName);
    }

    public function printInvoice($subscriptionId)
    {
        $user         = getParentUser();
        $subscription = PlanPurchase::where('user_id', $user->id)->with(['subscriptionPlan', 'user'])->find($subscriptionId);
        $pageTitle    = "Print invoice";
        $html         = view('Template::user.subscription.print-invoice', compact('subscription', 'pageTitle'))->render();
        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    private function manageFreePlanPurchase($user, $plan)
    {
        if ($user->plan_id) {
            $notify[] = ['error', 'You have already subscribed a plan, you can not subscribe again to a free plan.'];
            return back()->withNotify($notify);
        }

        $planPurchase                       = new PlanPurchase();
        $planPurchase->user_id              = $user->id;
        $planPurchase->amount               = 0;
        $planPurchase->subscription_plan_id = $plan->id;
        $planPurchase->recurring_type       = Status::MONTHLY;
        $planPurchase->expired_at           = Carbon::now()->addMonths(1);
        $planPurchase->save();


        $user->plan_id         = $plan->id;
        $user->plan_expired_at = Carbon::now()->addMonths(1);
        $user->product_limit   = $plan->product_limit   == Status::UNLIMITED ? Status::UNLIMITED : $user->product_limit + $plan->product_limit;
        $user->user_limit      = $plan->user_limit      == Status::UNLIMITED ? Status::UNLIMITED : $user->user_limit + $plan->user_limit;
        $user->warehouse_limit = $plan->warehouse_limit == Status::UNLIMITED ? Status::UNLIMITED : $user->warehouse_limit + $plan->warehouse_limit;
        $user->supplier_limit  = $plan->supplier_limit  == Status::UNLIMITED ? Status::UNLIMITED : $user->supplier_limit + $plan->supplier_limit;
        $user->coupon_limit    = $plan->coupon_limit    == Status::UNLIMITED ? Status::UNLIMITED : $user->coupon_limit + $plan->coupon_limit;
        $user->hrm_access      = $plan->hrm_access;
        $user->save();

        $notify[] = ['success', 'You have successfully subscribed to a free plan.'];
        return back()->withNotify($notify);
    }
}
