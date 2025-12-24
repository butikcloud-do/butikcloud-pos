<?php

namespace App\Traits;

use App\Models\Category;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait CategoryOperation
{
    public function list()
    {

        $user      = getParentUser();
        $pageTitle = 'Manage Category';
        $view      = "Template::user.category.list";
        $baseQuery = Category::where('user_id', $user->id)->searchable(['name'])->orderBy('id', getOrderBy());

        if (request()->trash) {
            $baseQuery->onlyTrashed();
        }

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Category");
        }

        $categories = $baseQuery->paginate(getPaginate());
        return responseManager("categories", $pageTitle, 'success', compact('categories', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
             'name' => ['required', 'string', 'max:40', Rule::unique('categories', 'name')->where('user_id', getParentUser()->id)->whereNull('deleted_at')->ignore($id)],
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $user = getParentUser();

        if ($id) {
            $category = Category::where('id', $id)->where('user_id', $user->id)->firstOrFailWithApi('category');
            $message  = "Category updated successfully";
            $remark   = "category-updated";
        } else {
            $category          = new Category();
            $message           = "Category saved successfully";
            $remark            = "category-insert";
            $category->user_id = $user->id;
        }

        $category->name = $request->name;

        if ($request->hasFile('image')) {
            try {
                $old             = $category->image;
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), $old);
            } catch (\Exception $exp) {
                $message = 'Couldn\'t upload your image';
                return responseManager('exception', $message);
            }
        }
        $category->save();
        adminActivity($remark, get_class($category), $category->id);
        return responseManager("category", $message, 'success', compact('category'));
    }

    public function status($id)
    {

        return Category::changeStatus($id);
    }
}
