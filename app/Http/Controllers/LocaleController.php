<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function getWizards(Request $request){
        $client = new Client();
        $resp = json_decode($client->get("https://maps.googleapis.com/maps/api/geocode/json?address={$request->get('zip')}&sensor=true&key=AIzaSyA5D4hMDFEC0LeERCZikKGGyiWShCkwJ3Y")->getBody()->getContents());

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
