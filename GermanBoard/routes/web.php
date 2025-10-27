<?php

use App\Http\Controllers\API\V1\Auth\GoogleController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('auth/google', [GoogleController::class, function () {
    return Socialite::driver('google')->redirect();
}]);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
