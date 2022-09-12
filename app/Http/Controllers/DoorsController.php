<?php

namespace App\Http\Controllers;

use App\Models\Faerie;
use App\Models\FaerieTemplate;
use App\Models\Locale;
use App\Services\AI;
use App\Services\Geocoding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DoorsController extends Controller
{

    public function __construct(private AI $aiService)
    {
    }

    public function create(){
        $template = FaerieTemplate::whereDoesntHave('faeries', function ($q){
            return $q->where('locale_id', Auth::user()->locale_id);
        })->inRandomOrder()->first();
        if ($template->bio == null){
            $template->bio = $this->aiService->getBio($template->name);
            $template->save();
        }
        return Inertia::render('Doors/CreateEdit', [
            'template'=>$template
        ]);
    }

    public function store(Request $request){
        $resp = Geocoding::getForLatLng($request->get('latitude'), $request->get('longitude'));
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
        $locale = Locale::where('name', $localeName)->where('state', $state)->firstOrCreate();
        $template = FaerieTemplate::find($request->get('template_id'));
        $faerie = new Faerie();
        $faerie->faerie_template_id = $request->get('template_id');
        $faerie->bio = $request->get('newBio');
        $faerie->latitude = $request->get('latitude');
        $faerie->longitude = $request->get('longitude');
        $faerie->name = $request->get('name');
        $faerie->locale_id = $locale->id;
        $faerie->user_id = Auth::id();
        $faerie->image_url = 'https://faerie-doors.s3.us-east-2.amazonaws.com/'.$template->image;
        $faerie->save();

        return redirect(route('dashboard'));
    }
}
