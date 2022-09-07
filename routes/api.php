<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/locale', [\App\Http\Controllers\LocaleController::class, 'getWizards']);
Route::get('/bio', function (Request $request){
    /** @var \App\Services\AI $ai */
    $ai = app(\App\Services\AI::class);
    return [
        'bio' => $ai->getBio($request->get('name'))
    ];
});
