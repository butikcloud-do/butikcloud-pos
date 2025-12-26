<?php

namespace App\Traits;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait CustomerOperation
{
    public function list()
    {
        $baseQuery = Customer::where('user_id', getParentUser()->id)->searchable(['name', 'email'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Customer';
        $view      = "Template::user.customer.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Customer", "A4 landscape");
        }

        $customers = $baseQuery->paginate(getPaginate());
        return responseManager("customer", $pageTitle, 'success', compact('customers', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {

        $parentId = getParentUser()->id;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('customers', 'email')
                    ->where(fn($query) => $query->where('user_id', $parentId))
                    ->when($request->email, fn($query) => $query->whereNotNull('email'))
                    ->ignore($id),
            ],
            'mobile'   => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers', 'mobile')
                    ->where(fn($query) => $query->where('user_id', $parentId))
                    ->ignore($id),
            ],
            'address'  => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'country'  => 'nullable|string|max:255',
            'zip'      => 'nullable|string|max:40',
            'postcode' => 'nullable|string|max:40',
        ]);


        if ($id) {
            $customer = Customer::where('id', $id)->where('user_id', $parentId)->firstOrFailWithApi('customer');
            $message  = "Customer updated successfully";
            $remark   = "customer-updated";
        } else {
            $customer          = new Customer();
            $message           = "Customer saved successfully";
            $remark            = "customer-added";
            $customer->user_id = $parentId;
        }

        $customer->name     = $request->name;
        $customer->email    = $request->email;
        $customer->mobile   = $request->mobile;
        $customer->address  = $request->address;
        $customer->city     = $request->city;
        $customer->state    = $request->state;
        $customer->country  = $request->country;
        $customer->zip      = $request->zip;
        $customer->postcode = $request->postcode;
        $customer->save();

        adminActivity($remark, get_class($customer), $customer->id);

        if (request()->from == 'pos') {
            return jsonResponse('success', 'success', (array) $message, [
                'customer' => $customer,
            ]);
        }

        return responseManager("customer", $message, 'success', compact('customer'));
    }

    public function status($id)
    {
        return Customer::changeStatus($id);
    }
}
