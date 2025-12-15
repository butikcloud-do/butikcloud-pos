<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\PaymentType;
use Illuminate\Http\Request;

trait PaymentTypeOperation
{
    public function list()
    {
        $user      = getParentUser();
        $baseQuery = PaymentType::where('user_id', $user->id)->searchable(['name'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Payment Type';
        $view      = "Template::user.payment_type.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "PaymentType");
        }

        $paymentTypes = $baseQuery->paginate(getPaginate());
        return responseManager("payment_type", $pageTitle, 'success', compact('paymentTypes', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'icon'    => 'required|string|max:255',

        ]);

        $user = getParentUser();
        if (PaymentType::where('user_id', $user->id)->where('name', $request->name)->where('id', '!=', $id)->exists()) {
            $message = "This payment type already exists. Please choose a different one.";
            return responseManager("already_exists", $message);
        }

        if ($id) {
            $paymentType = PaymentType::where('id', $id)->where('is_default', Status::NO)->where('user_id', $user->id)->firstOrFailWithApi('Payment type');
            $message     = "Payment type updated successfully";
            $remark      = "payment-type-updated";

            if ($paymentType->is_default != Status::YES) {
                $paymentType->name    = $request->name;
                $paymentType->variant = $request->variant;
                $paymentType->icon    = $request->icon;
            }
        } else {
            $paymentType          = new PaymentType();
            $message              = "Payment type saved successfully";
            $remark               = "payment-type-added";
            $paymentType->user_id = $user->id;
            $paymentType->name    = $request->name;
            $paymentType->variant = $request->variant;
            $paymentType->icon    = $request->icon;
        }

        $paymentType->save();

        adminActivity($remark, get_class($paymentType), $paymentType->id);
        return responseManager("payment_type", $message, 'success', compact('paymentType'));
    }

    public function status($id)
    {
        $paymentType = PaymentType::where('user_id', getParentUser()->id)->where('id', $id)->where('is_default', Status::NO)->firstOrFailWithApi('Payment type');

        return PaymentType::changeStatus($id);
    }
}
