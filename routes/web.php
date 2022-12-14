<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoorsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('doors', DoorsController::class);
    Route::get('/qr/{uuid}', function ($uuid){

        return Inertia::render('Doors/QR', [
            'uuid'=>$uuid
        ]);
    })->name('qr');

});
Route::get('mailtest', function (){
    \Illuminate\Support\Facades\Mail::raw('Text to e-mail', function($message)
    {
        $message->from('grandmastser@faeriedoors.com', 'Grand Master');

        $message->to('heckel.max@gmail.com')->subject('New faerie message');
    });
});
Route::get('/faerie/{slug}', [DoorsController::class, 'getPublic'])->name('doors.public');
Route::post('/faerie/{slug}', [CommentController::class, 'createComment'])->name('leave-comment');
Route::post('/comments/{id}', [CommentController::class, 'createReply'])->name('leave-reply');
