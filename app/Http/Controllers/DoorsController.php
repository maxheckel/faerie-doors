<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Faerie;
use App\Models\FaerieTemplate;
use App\Models\Locale;
use App\Services\AI;
use App\Services\Geocoding;
use App\Services\Profanity;
use App\Services\Weather;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

use Ramsey\Uuid\Uuid;
use Snipe\BanBuilder\CensorWords;

class DoorsController extends Controller
{

    public function __construct(private AI $aiService, private Weather $weatherService)
    {
    }

    public function create(Request $request){

        if (Session::has('profanity') && Session::get('profanity') === true){
            $template = FaerieTemplate::find($request->old('template_id'));
            $template->bio = $request->old('bio');
            $template->name = $request->old('name');
        } else {
            $template = FaerieTemplate::whereDoesntHave('faeries', function ($q){
                return $q->where('locale_id', Auth::user()->locale_id);
            })->inRandomOrder()->first();
            if ($template->bio == null){
                $template->bio = $this->aiService->getBio($template->name);
                $template->save();
            }
        }


        return Inertia::render('Doors/CreateEdit', [
            'template'=>$template,
            'profanity' => Session::get('profanity')
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
        $profanityCheck = Profanity::hasProfanity($request->get('newBio'));
        if ($profanityCheck !== null){
            $request->request->set('newBio', $profanityCheck);
            $request->request->set('bio', $profanityCheck);
            return redirect()->back()->withInput($request->all())->with('profanity', true);
        }
        $faerie->bio = $request->get('newBio');
        $faerie->latitude = $request->get('latitude');
        $faerie->longitude = $request->get('longitude');
        $faerie->name = $request->get('name');
        $faerie->locale_id = $locale->id;
        $faerie->uuid = Uuid::uuid4();
        $faerie->user_id = Auth::id();
        $faerie->image_url = 'https://faerie-doors.s3.us-east-2.amazonaws.com/'.$template->image;
        $faerie->save();

        return redirect(route('dashboard'));
    }

    public function show(Request $request, $id){
        $faerie = Faerie::where('id', $id)->with(['comments' => function($q){
            return $q->where('public', true)->orderBy('created_at', 'desc');
        }, 'comments.comments'])->firstOrFail();
        if ($faerie->user_id != Auth::id()){
            abort(403);
        }
//        $this->weatherService->getCurrentWeather($faerie->latitude, $faerie->longitude);
        $maxDistance = 0.02;
        $otherFaeries = Faerie::where(function ($q) use ($faerie, $maxDistance){
            $q->where('latitude', '>=', $faerie->latitude-$maxDistance)->orWhere('latitude', '<=', $faerie->latitude+$maxDistance);
        })->where(function ($q) use ($faerie, $maxDistance){
            $q->where('longitude', '>=', $faerie->longitude-$maxDistance)->orWhere('longitude', '<=', $faerie->longitude+$maxDistance);
        })->where('id', '!=', $faerie->id)->get();

        return Inertia::render('Doors/View', [
            'faerie' => $faerie,
            'profanity' => Session::get('profanity'),
            'messageSent' => Session::get('messageSent'),
            'old' => $request->old(),
            'otherFaeries' => $otherFaeries
        ]);
    }

    public function update(Request $request, $id){
        $faerie = Faerie::find($id);
        if ($faerie->user_id != Auth::id()){
            abort(403);
        }
        $profanityCheck = Profanity::hasProfanity($request->get('newBio'));
        if ($profanityCheck !== null){
            $request->request->set('newBio', $profanityCheck);
            $request->request->set('bio', $profanityCheck);
            return redirect()->back()->withInput($request->all())->with('profanity', true);
        }

        $faerie->bio = $request->get('newBio');
        $faerie->latitude = $request->get('latitude');
        $faerie->longitude = $request->get('longitude');
        $faerie->name = $request->get('name');
        $faerie->save();
        return redirect()->to(route('dashboard'));
    }

    public function edit(Request $request, $id){
        $faerie = Faerie::find($id);
        if ($faerie->user_id != Auth::id()){
            abort(403);
        }
        return Inertia::render('Doors/CreateEdit', [
            'faerie' => $faerie
        ]);
    }

    public function getPublic(Request $request, $slug){
        $faerie = Faerie::where('uuid', $slug)->with(['comments' => function($q){
            return $q->where('public', true)->orderBy('created_at', 'desc');
        }, 'comments.comments'])->firstOrFail();

        $maxDistance = 0.02;
        $otherFaeries = Faerie::where(function ($q) use ($faerie, $maxDistance){
            $q->where('latitude', '>=', $faerie->latitude-$maxDistance)->orWhere('latitude', '<=', $faerie->latitude+$maxDistance);
        })->where(function ($q) use ($faerie, $maxDistance){
            $q->where('longitude', '>=', $faerie->longitude-$maxDistance)->orWhere('longitude', '<=', $faerie->longitude+$maxDistance);
        })->where('id', '!=', $faerie->id)->get();

        return Inertia::render('Doors/Public', [
            'faerie' => $faerie,
            'profanity' => Session::get('profanity'),
            'messageSent' => Session::get('messageSent'),
            'old' => $request->old(),
            'otherFaeries' => $otherFaeries
        ]);
    }

}
