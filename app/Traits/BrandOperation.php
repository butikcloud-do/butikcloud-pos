<?php

namespace App\Traits;

use App\Models\Brand;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait BrandOperation
{
    public function list()
    {
        $user      = getParentUser();
        $pageTitle = 'Manage Brand';
        $view      = "Template::user.brand.list";
        $baseQuery = Brand::where('user_id', $user->id)->searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "brand");
        }

        $brands = $baseQuery->paginate(getPaginate());

        return responseManager("brands", $pageTitle, 'success', compact('brands', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $user = getParentUser();

        $request->validate([
            'name'  => [
                'required',
                'string',
                'max:40',
                //  Rule::unique('brands', 'name')->where('user_id', $user->id)->ignore($id),
                Rule::unique('brands', 'name')->where('user_id', $user->id)->whereNull('deleted_at')->ignore($id),
            ],
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $brand   = Brand::where('id', $id)->where('user_id', $user->id)->firstOrFailWithApi('brand');
            $message = "Brand updated successfully";
            $remark  = "brand-updated";
        } else {
            $brand          = new Brand();
            $message        = "Brand saved successfully";
            $remark         = "brand-insert";
            $brand->user_id = $user->id;
        }

        if ($request->hasFile('image')) {
            try {
                $old          = $brand->image;
                $brand->image = fileUploader($request->image, getFilePath('brand'), getFileSize('brand'), $old);
            } catch (\Exception $exp) {
                $message = 'Couldn\'t upload your image';
                return responseManager('exception', $message);
            }
        }

        $brand->name = $request->name;
        $brand->save();

        adminActivity($remark, get_class($brand), $brand->id);

        return responseManager("brand", $message, 'success', compact('brand'));
    }

    public function status($id)
    {
        return Brand::changeStatus($id);
    }
}
