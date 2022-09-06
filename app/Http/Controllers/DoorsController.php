<?php

namespace App\Http\Controllers;

use App\Models\FaerieTemplate;
use App\Services\AI;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoorsController extends Controller
{

    public function __construct(private AI $aiService)
    {
    }

    public function create(){
        $template = FaerieTemplate::where('used', false)->inRandomOrder()->first();
        if ($template->bio == null){
            $template->bio = $this->aiService->getBio($template->name);
            $template->save();
        }
        return Inertia::render('Doors/CreateEdit', [
            'template'=>$template
        ]);
    }
}
