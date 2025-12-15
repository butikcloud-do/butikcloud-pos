<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Traits\StockTransferOperation;


class StockTransferController extends Controller
{
    use StockTransferOperation;

    public function print($id)
    {
        $transfer  = StockTransfer::where("id", $id)->with(['toWarehouse', 'fromWarehouse', 'admin', 'stockTransferDetails'])->first();
        if (!$transfer) {
            $message[] = "The stock transfer is not found";
            return jsonResponse('not_found', 'error', $message);
        }
        $view      = "Template::user.stock_transfer.invoice";
        $message[] = "Print Invoice";

        return jsonResponse('print', 'success', $message, [
            'html' => view($view, compact('transfer'))->render()
        ]);
    }


}
