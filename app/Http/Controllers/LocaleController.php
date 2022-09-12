<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use App\Services\Geocoding;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function getWizards(Request $request){
        $resp = Geocoding::getForZip($request->get('zip'));
        $resp = collect($resp->results[0]->address_components);
        $localeName = '';
        $state = '';
        foreach ($resp as $component){
            if (in_array('locality', $component->types)){
                $localeName = strtolower($component->short_name);
            }
            if (in_array('administrative_area_level_1', $component->types)){
                $state = strtolower($component->long_name);
            }
        }
        $localeCheck = Locale::where('name', $localeName)->where('state', $state)->with('wizards')->first();

        return [
            'name' => $localeName,
            'state' => $state,
            'wizards' => $localeCheck?->wizards->count() ?? 0
        ];
    }


}
