<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Traits\SaleOperation;


class SaleController extends Controller
{

    use SaleOperation;

    public function print($id)
    {

        $sale = Sale::where('user_id', getParentUser()->id)
            ->withSum('payments', 'amount')
            ->where("id", $id)
            ->with("warehouse", "customer")
            ->first();

        if (!$sale) {
            $message[] = "The sale is not found";
            return jsonResponse('not_found', 'error', $message);
        }
        $view      = "Template::user.sale.invoice";
        $message[] = "Print Invoice";

        if (request()->invoice_type == 'pos') {
            $view = "Template::user.sale.pos_invoice";
        }

        return jsonResponse('print', 'success', $message, [
            'html' => view($view, compact('sale'))->render()
        ]);
    }
}
