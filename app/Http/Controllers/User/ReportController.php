<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\ReportOperation;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    use ReportOperation;

    public function notificationHistory(Request $request)
    {
        $pageTitle = 'Notification History';
        $baseQuery = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->filter(['user_id'])->dateFilter();
        if (request()->export) {
            return exportData($baseQuery, request()->export, "NotificationLog");
        }
        $logs = $baseQuery->with('user')->paginate(getPaginate());
        return view('Template::user.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email     = NotificationLog::findOrFail($id);
        return view('Template::user.reports.email_details', compact('pageTitle', 'email'));
    }

    public function transaction(Request $request)
    {
        $pageTitle = 'Transaction History';
        $user      = getParentUser();

        $baseQuery = Transaction::where('user_id', $user->id)->searchable(['trx'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', getOrderBy());

        if (request()->payment_type) {
            $baseQuery->whereHas('paymentAccount', function ($q) {
                $q->where('payment_type_id', request()->payment_type);
            });
        }

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Transaction");
        }

        $transactions = $baseQuery->with('paymentAccount.paymentType')->paginate(getPaginate());

        return view('Template::user.reports.transactions', compact('pageTitle', 'transactions'));
    }
}
