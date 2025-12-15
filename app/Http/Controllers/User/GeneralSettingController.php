<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class GeneralSettingController extends Controller
{
    public function general()
    {
        $pageTitle       = 'General Setting';
        $user            = getParentUser();
        $timezones       = timezone_identifiers_list();
        $currentTimezone = array_search(config('app.timezone'), $timezones);
        $generalSetting  = GeneralSetting::where('user_id', $user->id)->first() ?? gs();


        return view('Template::user.setting.general', compact('pageTitle', 'timezones', 'currentTimezone', 'generalSetting'));
    }

    public function invoiceSetting()
    {
        $pageTitle = 'Prefix Setting';
        $user      = getParentUser();
        $prefix    = GeneralSetting::where('user_id', $user->id)->first();
        return view('Template::user.setting.invoice', compact('pageTitle', 'prefix'));
    }
    public function companySetting()
    {
        $pageTitle          = 'Company Setting';
        $user               = getParentUser();
        $companyInformation = GeneralSetting::where('user_id', $user->id)->first();
        return view('Template::user.setting.company', compact('pageTitle', 'companyInformation'));
    }

    public function companySettingUpdate(Request $request)
    {
        $request->validate([
            'company_information'         => 'required|array',
            'company_information.name'    => 'required',
            'company_information.phone'   => 'required',
            'company_information.address' => 'required',
            'company_information.email'   => 'nullable|email',
        ]);

        $user = getParentUser();
        $gs   = GeneralSetting::where('user_id', $user->id)->first();

        if (!$gs) {
            $gs          = new GeneralSetting();
            $gs->user_id = $user->id;
        }

        $gs->company_information = $request->company_information;
        $gs->save();


        cache()->forget('GeneralSetting_' . $user->id);
        cache()->put('GeneralSetting_' . $user->id, $gs);

        $notify[] = ['success', 'Company information updated successfully'];
        adminActivity("company-information-updated", get_class($gs), $gs->id);
        return back()->withNotify($notify);
    }
    public function generalUpdate(Request $request)
    {
        $request->validate([
            'cur_text'           => 'required|string|max:40',
            'cur_sym'            => 'required|string|max:40',
            'timezone'           => 'required|integer',
            'currency_format'    => 'required|in:1,2,3',
            'paginate_number'    => 'required|integer',
            'time_format'        => ['required', Rule::in(supportedTimeFormats())],
            'date_format'        => ['required', Rule::in(supportedDateFormats())],
            'thousand_separator' => ['required', Rule::in(array_keys(supportedThousandSeparator()))],
            'allow_precision'    => 'required|integer|gt:0|lte:8',
        ]);

        $timezones = timezone_identifiers_list();
        $timezone  = @$timezones[$request->timezone] ?? 'UTC';
        $user      = getParentUser();


        $general = GeneralSetting::where('user_id', $user->id)->first();

        if (!$general) {
            $general          = new GeneralSetting();
            $general->user_id = $user->id;
        }

        $general->cur_text           = $request->cur_text;
        $general->cur_sym            = $request->cur_sym;
        $general->paginate_number    = $request->paginate_number;
        $general->currency_format    = $request->currency_format;
        $general->time_format        = $request->time_format;
        $general->date_format        = $request->date_format;
        $general->allow_precision    = $request->allow_precision;
        $general->thousand_separator = $request->thousand_separator;
        $general->timezone           = $timezone;
        $general->save();


        cache()->forget('GeneralSetting_' . $user->id);
        cache()->put('GeneralSetting_' . $user->id, $general);

        $notify[] = ['success', 'General setting updated successfully'];

        adminActivity("generate-setting-updated", get_class($general), $general->id);
        return back()->withNotify($notify);
    }

    public function invoiceSettingUpdate(Request $request)
    {
        $request->validate([
            'product_code_prefix'           => 'required',
            'purchase_invoice_prefix'       => 'required',
            'sale_invoice_prefix'           => 'required',
            'stock_transfer_invoice_prefix' => 'required',
        ]);

        $prefixSetting = [
            'purchase_invoice_prefix'       => $request->purchase_invoice_prefix,
            'sale_invoice_prefix'           => $request->sale_invoice_prefix,
            'product_code_prefix'           => $request->product_code_prefix,
            'stock_transfer_invoice_prefix' => $request->stock_transfer_invoice_prefix,
        ];

        $user = getParentUser();

        $general = GeneralSetting::where('user_id', $user->id)->first();

        if (!$general) {
            $general          = new GeneralSetting();
            $general->user_id = $user->id;
        }

        $general->prefix_setting = $prefixSetting;
        $general->save();


        cache()->forget('GeneralSetting_' . $user->id);
        cache()->put('GeneralSetting_' . $user->id, $general);


        adminActivity("prefix-setting-updated", get_class($general), $general->id);
        $notify[] = ['success', 'Prefix setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function logoIcon()
    {
        $pageTitle = 'Brand Setting';
        $user      = getParentUser();
        $general   = GeneralSetting::where('user_id', $user->id)->first();
        return view('Template::user.setting.logo_icon', compact('pageTitle', 'general'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo_light'   => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'logo_dark'    => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $user    = getParentUser();
        $general = GeneralSetting::where('user_id', $user->id)->first();

        if (!$general) {
            $general          = new GeneralSetting();
            $general->user_id = $user->id;
        }

        if ($request->hasFile('logo_light')) {
            try {
                $path                = getFilePath('logoIcon');
                $general->logo_light = fileUploader($request->logo_light, $path);
            } catch (\Exception $exp) {
                $message[] = "Couldn\'t upload your image";
                return jsonResponse('exception', 'error', $message);
            }
        }
        if ($request->hasFile('logo_dark')) {
            try {
                $path               = getFilePath('logoIcon');
                $general->logo_dark = fileUploader($request->logo_dark, $path);
            } catch (\Exception $exp) {
                $message[] = "Couldn\'t upload your image";
                return jsonResponse('exception', 'error', $message);
            }
        }

        $general->save();


        cache()->forget('GeneralSetting_' . $user->id);
        cache()->put('GeneralSetting_' . $user->id, $general);

        $notify[] = ['success', 'Brand setting updated successfully'];
        return back()->withNotify($notify);
    }
}
