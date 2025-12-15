<?php

namespace App\Traits;

use App\Constants\Status;
use Carbon\Carbon;

trait UserNotify
{
    public static function notifyToUser()
    {
        return [
            'allUsers'                       => 'All Users',
            'selectedUsers'                  => 'Selected Users',
            'pendingTicketUser'              => 'Pending Ticket Users',
            'answerTicketUser'               => 'Answer Ticket Users',
            'closedTicketUser'               => 'Closed Ticket Users',
            'notLoginUsers'                  => 'Last Few Days Not Login Users',
            'subscriptionExpiredUser'        => 'User whose subscription has been expired',
            'SubscriptionWillExpireIn3Days'  => 'User whose subscription will expire in 3 days',
            'SubscriptionWillExpireIn7Days'  => 'User whose subscription will expire in 7 days',
            'SubscriptionWillExpireIn15Days' => 'User whose subscription will expire in 15 days',
        ];
    }

    public function scopeSelectedUsers($query)
    {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllUsers($query)
    {
        return $query;
    }

    public function scopeEmptyBalanceUsers($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableUsers($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedUsers($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
    }

    public function scopeHasWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginUsers($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeSubscriptionExpiredUser($query)
    {
        $query->whereNotNull("expired_at")->where('expired_at', '<=', Carbon::now());
    }

    public function scopeSubscriptionWillExpireIn3Days($query)
    {
        $query->whereNotNull("expired_at")->whereBetween('expired_at', [now(), now()->addDays(3)]);
    }

    public function scopeSubscriptionWillExpireIn7Days($query)
    {
        $query->whereNotNull("expired_at")->whereBetween('expired_at', [now(), now()->addDays(7)]);
    }

    public function scopeSubscriptionWillExpireIn15Days($query)
    {
        $query->whereNotNull("expired_at")->whereBetween('expired_at', [now(), now()->addDays(15)]);
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }
}
