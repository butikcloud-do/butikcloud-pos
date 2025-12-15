<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\AdminOperation;

class AppController extends Controller
{
    use AdminOperation;
    public function generalSetting()
    {
        $notify[] = 'General setting data';
        return jsonResponse("general_setting", "success", $notify, [
            'general_setting' => gs(),
        ]);
    }

    public function getCountries()
    {
        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $notify[]    = 'Country List';

        foreach ($countryData as $k => $country) {
            $countries[] = [
                'country'      => $country->country,
                'dial_code'    => $country->dial_code,
                'country_code' => $k,
            ];
        }
        return jsonResponse("country_data", "success", $notify, [
            'countries' => $countries
        ]);
    }

    public function getLanguage($code)
    {
        $languages     = Language::get();
        $languageCodes = $languages->pluck('code')->toArray();

        if (!in_array($code, $languageCodes)) {
            $notify[] = 'Invalid code given';
            return jsonResponse("invalid_code", "error", $notify);
        }

        $jsonFile = file_get_contents(resource_path('lang/' . $code . '.json'));
        $notify[] = 'Language';

        return jsonResponse("language", "success", $notify, [
            'languages'  => $languages,
            'file'       => json_decode($jsonFile) ?? [],
            'image_path' => getFilePath('language')
        ]);
    }

  
}
