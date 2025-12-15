<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Traits\PurchaseOperation;

class PurchaseController extends Controller
{
    use PurchaseOperation;

    public function print($id)
    {
        $purchase = Purchase::withSum('supplierPayments', 'amount')
            ->where('user_id', getParentUser()->id)
            ->where("id", $id)
            ->with("warehouse", "supplier", 'supplierPayments.paymentType')
            ->first();

        if (!$purchase) {
            $message[] = "The purchase is not found";
            return jsonResponse('not_found', 'error', $message);
        }

        $message[] = "Print Invoice";
        return jsonResponse('print', 'success', $message, [
            'html' => view('Template::user.purchase.invoice', compact('purchase'))->render()
        ]);
    }
}
