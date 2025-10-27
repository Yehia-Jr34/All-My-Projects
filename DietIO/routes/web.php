<?php

use App\Events\DoctorSentMessage;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\ImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('DoctorSentMessage', function () {
    return view('RealTime/DoctorSentMessage');
});

Route::get('UserSentMessage', function () {
    return view('RealTime/UserSentMessage');
});

Route::get('DoctorRequestToJoin', function () {
    return view('RealTime/DoctorRequestToJoin');
});

Route::get('UserRequestsAndResponse', function () {
    return view('RealTime/UserRequestsAndResponse');
});

Route::get('UserRegistered', function () {
    return view('RealTime/UserRegistered');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('verify', function (Request $request) {
    return view('VerifyYourEmail');
})->name('verify');

Route::get('auth/google/redirect', function (Request $request) {
    return Socialite::driver('google')->redirect();
});
Route::get('/auth/google/callback', [UserAuthController::class, 'loginWithGoogle']);

