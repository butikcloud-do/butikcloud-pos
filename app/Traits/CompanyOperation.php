<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait CompanyOperation
{
    public function list()
    {
        $user = getPArentUser();

        $baseQuery = Company::where('user_id', $user->id)->searchable(['name', 'email', 'mobile'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Company';
        $view      = "Template::user.hrm.company.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Company", "A4 landscape");
        }
        $companies = $baseQuery->paginate(getPaginate());

        return responseManager("company", $pageTitle, 'success', compact('companies', 'view', 'pageTitle'));
    }


    public function save(Request $request, $id = 0)
    {
        $getParentUser = getParentUser();

        $request->validate([
            'name' => ['required', 'string', 'max:40', Rule::unique('companies', 'name')->where('user_id', $getParentUser->id)->ignore($id)],
            'email' => ['nullable', 'string', 'email', 'max:40', Rule::unique('companies', 'email')->where('user_id', $getParentUser->id)->ignore($id)],
            'mobile' => ['nullable', 'string', 'max:40', Rule::unique('companies', 'mobile')->where('user_id', $getParentUser->id)->ignore($id)],
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);


        if ($id) {
            $company = Company::where('id', $id)->where('user_id', $getParentUser->id)->firstOrFailWithApi('company');
            $message = "Company updated successfully";
            $remark  = "company-updated";
        } else {
            $company          = new Company();
            $message          = "Company saved successfully";
            $remark           = "company-added";
            $company->user_id = $getParentUser->id;
        }

        $company->name    = $request->name;
        $company->email   = $request->email;
        $company->mobile  = $request->mobile;
        $company->country = $request->country;
        $company->address = $request->address;
        $company->save();

        adminActivity($remark, get_class($company), $company->id);

        return responseManager("company", $message, 'success', compact('company'));
    }

    public function status($id)
    {
        return Company::changeStatus($id);
    }
}
