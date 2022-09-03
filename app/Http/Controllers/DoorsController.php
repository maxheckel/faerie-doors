<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DoorsController extends Controller
{
    public function create(){
        return Inertia::render('Doors/CreateEdit');
    }
}
