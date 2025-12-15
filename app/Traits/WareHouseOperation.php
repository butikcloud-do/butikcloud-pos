<?php

namespace App\Traits;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait WareHouseOperation
{

    public $modelName = "Warehouse";

    public function list()
    {

        $baseQuery = Warehouse::where('user_id', getParentUser()->id)->searchable(['name', 'contact_number'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Warehouse';
        $view      = "Template::user.warehouse.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Warehouse", "A4 landscape");
        }

        $warehouses = $baseQuery->paginate(getPaginate());
        return responseManager("warehouse", $pageTitle, 'success', compact('warehouses', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'=> ['required', 'string', Rule::unique('warehouses', 'name')->where('user_id', getParentUser()->id)->ignore($id)],
            'address'        => 'required|string',
            'contact_number' => 'required|string',
            'city'           => 'nullable|string',
            'state'          => 'nullable|string',
            'postcode'       => 'nullable|string',
        ]);

        $parentUser = getParentUser();

        if ($id) {
            $warehouse = Warehouse::where('id', $id)->where('user_id', $parentUser->id)->firstOrFailWithApi('Warehouse');
            $message   = "Warehouse updated successfully";
            $remark    = "warehouse-updated";
        } else {

            if (!featureAccessLimitCheck($parentUser->warehouse_limit)) {
                $message = "You have reached the maximum limit of adding warehouse. Please upgrade your plan.";
                return responseManager("subscription_reached", $message, "error");
            }

            $warehouse          = new Warehouse();
            $message            = "Warehouse saved successfully";
            $remark             = "warehouse-added";
            $warehouse->user_id = $parentUser->id;
        }

        $warehouse->contact_number = $request->contact_number;
        $warehouse->name           = $request->name;
        $warehouse->address        = $request->address;
        $warehouse->city           = $request->city;
        $warehouse->state          = $request->state;
        $warehouse->postcode       = $request->postcode;
        $warehouse->save();

        adminActivity($remark, get_class($warehouse), $warehouse->id);

        if (!$id) {
            decrementFeature($parentUser, 'warehouse_limit');
        }

        return responseManager("warehouse", $message, 'success', compact('warehouse'));
    }

    public function status($id)
    {
        return Warehouse::changeStatus($id);
    }
}
