<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $pageTitle         = 'Subscription Plans';
        $subscriptionPlans = SubscriptionPlan::orderBy('id', getOrderBy())->paginate(getPaginate());
        return view('admin.subscription.index', compact('pageTitle', 'subscriptionPlans'));
    }

    public function create()
    {
        $pageTitle    = 'Add Plan';
        return view('admin.subscription.add', compact('pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'            => 'required|string|unique:subscription_plans,name,' . $id,
            'hrm_access'      => 'required',
            'product_limit'   => 'required|numeric|gte:-1',
            'user_limit'      => 'required|numeric|gte:-1',
            'warehouse_limit' => 'required|numeric|gte:-1',
            'supplier_limit'  => 'required|numeric|gte:-1',
            'coupon_limit'    => 'required|numeric|gte:-1',
            'monthly_price'   => 'required|numeric|gte:0',
            'yearly_price'    => 'required|numeric|gte:monthly_price',
        ]);

        if ($id) {
            $plan    = SubscriptionPlan::findOrFail($id);
            $message = "Subscription plan updated successfully";
        } else {
            $plan    = new SubscriptionPlan();
            $message = "Subscription plan created successfully";
        }

        $plan->name            = $request->name;
        $plan->hrm_access      = $request->hrm_access;
        $plan->product_limit   = $request->product_limit;
        $plan->user_limit      = $request->user_limit;
        $plan->warehouse_limit = $request->warehouse_limit;
        $plan->supplier_limit  = $request->supplier_limit;
        $plan->coupon_limit    = $request->coupon_limit;
        $plan->monthly_price   = $request->monthly_price;
        $plan->yearly_price    = $request->yearly_price;
        $plan->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Plan';
        $plan      = SubscriptionPlan::findOrFail($id);
        return view('admin.subscription.add', compact('pageTitle', 'plan'));
    }

    public function status($id)
    {
        return SubscriptionPlan::changeStatus($id);
    }
}
