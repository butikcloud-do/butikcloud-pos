<?php

namespace App\Traits;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait AttributeOperation
{
    public function list()
    {
        $user      = getParentUser();
        $baseQuery = Attribute::where('user_id', $user->id)->searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());

        $pageTitle = 'Manage Attribute';
        $view      = "Template::user.attribute.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Attribute");
        }
        $attributes = $baseQuery->get();
        return responseManager("attributes", $pageTitle, 'success', compact('attributes', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            // 'name' => ['required', 'string', 'max:255', Rule::unique('attributes', 'name')->where('user_id', getParentUser()->id)->ignore($id)],
            'name' => ['required', 'string', 'max:255', Rule::unique('attributes', 'name')->where('user_id', getParentUser()->id)->whereNull('deleted_at')->ignore($id)],
        ]);

        $user = getParentUser();

        if ($id) {
            $attribute = Attribute::where('id', $id)->where('user_id', $user->id)->firstOrFailWithApi('attribute');
            $message = "Attribute updated successfully";
            $remark  = "attribute-updated";
        } else {
            $attribute          = new Attribute();
            $message            = "Attribute saved successfully";
            $remark             = "attribute-updated";
            $attribute->user_id = $user->id;
        }

        $attribute->name = $request->name;
        $attribute->save();
        adminActivity($remark, get_class($attribute), $attribute->id);
        return responseManager("attribute", $message, 'success', compact('attribute'));
    }

    public function status($id)
    {
        return Attribute::changeStatus($id);
    }
}
