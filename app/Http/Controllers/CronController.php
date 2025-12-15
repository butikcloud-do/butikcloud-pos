<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\PlanPurchase;
use Carbon\Carbon;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function subscriptionExpired()
    {
        $expiredSubscriptions = PlanPurchase::with(['user', 'subscriptionPlan'])->where('expired_at', '<=', Carbon::now())->where('is_sent_expired_notify', Status::NO)->get();

        foreach ($expiredSubscriptions as $subscription) {

            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;

            if (!$user || !$plan) {
                continue;
            }

            $subscription->is_sent_expired_notify = Status::YES;
            $subscription->save();

            $user->product_limit   = 0;
            $user->user_limit      = 0;
            $user->warehouse_limit = 0;
            $user->supplier_limit  = 0;
            $user->coupon_limit    = 0;
            $user->hrm_access      = 0;

            $user->save();

            notify($user, 'SUBSCRIPTION_EXPIRED', [
                'subscription_type' => $subscription->billing_cycle,
                'subscription_url'  => route('user.subscription.index'),
                'plan_name'         => $plan->name,
                'amount'            => showAmount($subscription->amount, currencyFormat: false),
                'expired_at'        => showDateTime($subscription->expired_at),
                'post_balance'      => showAmount($user->balance, currencyFormat: false),
            ]);
        }
    }

    public function subscriptionNotify()
    {
        $targetDate    = Carbon::now()->addDays(gs('subscription_notify_before'))->startOfDay()->format('Y-m-d');
        $subscriptions = PlanPurchase::with(['user', 'subscriptionPlan'])
            ->whereDate('expired_at', $targetDate)
            ->where('is_sent_reminder_notify', Status::NO)
            ->get();

        foreach ($subscriptions as $subscription) {
            $user          = $subscription->user;
            $purchasePrice = getPurchasePrice($subscription->subscriptionPlan, $subscription->recurring_type);

            notify($user, 'UPCOMING_EXPIRED_SUBSCRIPTION', [
                'subscription_type' => $subscription->billing_cycle,
                'subscription_url'  => route('user.subscription.index', ['tab' => 'current-plan']),
                'plan_name'         => $subscription->subscriptionPlan->name,
                'plan_price'        => showAmount($purchasePrice, currencyFormat: false),
                'next_billing'      => showDateTime($subscription->expired_at, 'd M Y'),
                'post_balance'      => showAmount($user->balance, currencyFormat: false),
            ]);
        }
    }

}
