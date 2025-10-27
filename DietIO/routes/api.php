<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\Api\DoctorAuthController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('users')->group(function () {
    Route::post('login', [UserAuthController::class, 'login']); //done
    Route::post('register', [UserAuthController::class, 'register']); //done
    Route::get('email/verify/{id}/{hash}', [UserAuthController::class, 'verify'])->name('verification.verify'); //done
    Route::get('forgot-password', [UserAuthController::class, 'showLinkRequestForm'])->name('password.request'); //done
    Route::post('/sendResetLinkEmail', [UserAuthController::class, 'sendResetLinkEmail'])->name('password.email'); //done
    Route::get('reset-password/{token}', [UserAuthController::class, 'showResetForm'])->name('password.reset'); //done
    Route::post('/reset-password', [UserAuthController::class, 'reset'])->name('password.reset'); //done
    Route::get('/verified/check', [UserAuthController::class, 'check_if_verified'])->middleware('auth:sanctum');


    Route::middleware(['auth:sanctum', 'verify'])->group(function () {
        Route::post('logout', [UserAuthController::class, 'logout']); //done
        Route::get('profile', [UserAuthController::class, 'profile']); //done
        Route::post('updatePassword', [UserAuthController::class, 'updatePassword']); //done
        Route::delete('/account/delete', [UserAuthController::class, 'delete']); //done
        Route::post('/account/update', [UserAuthController::class, 'updateProfile']); //done
        Route::post('/file/create', [UserController::class, 'create_file']);//done
        Route::post('/file/update', [UserController::class, 'update_file']);//done
        Route::get('file/view', [UserController::class, 'view_file']);
        Route::get('doctors/all', [UserController::class, 'view_doctors']);//done
        Route::post('doctors/addToFavorite', [UserController::class, 'add_doctor_to_fav']);//done
        Route::get('doctors/viewMyFavorites', [UserController::class, 'view_my_favorite']);//done
        Route::post('doctors/deleteFavorite', [UserController::class, 'delete_fav']);
        Route::post('doctors/rate', [UserController::class, 'rete_doctor']);//done
        Route::get('doctors/myRates', [UserController::class, 'view_my_rates']);//done
        Route::post('doctors/request', [UserController::class, 'consultation_request']);//done
        Route::get('doctors/viewMyRequests', [UserController::class, 'view_my_requests']);//done
        Route::post('doctors/cancelRequest', [UserController::class, 'cancel_request']);//done
        Route::get('doctors/paymentRequests', [UserController::class, 'view_payment_requests']);//done
        Route::get('diets/myDiets', [UserController::class, 'view_my_diets']);//done
        Route::post('diets/answerReview', [UserController::class, 'answer_review']);//done
        Route::post('search', [UserController::class, 'search_doctor']);//done
        Route::post('chat/create', [ChatController::class, 'create_chat']);//done
        Route::post('chat/send_message', [ChatController::class, 'send']);//done
        Route::get('chat/messages', [ChatController::class, 'getChatMessages']);
        Route::post('image/add', [ImagesController::class, 'addImage']);
        Route::post('payment/PayToDoctor', [PaymentController::class, 'makePayment']);
    });
});


Route::prefix('doctors')->group(function () {
    Route::post('login', [DoctorAuthController::class, 'login']); //done
    Route::post('register', [DoctorAuthController::class, 'registrationRequest']); //done
    Route::get('forgot-password', [DoctorAuthController::class, 'showLinkRequestForm'])->name('password.request');//done
    Route::post('sendResetLinkEmail', [DoctorAuthController::class, 'sendResetLinkEmail']);//done
    Route::get('reset-password/{token}', [DoctorAuthController::class, 'showResetForm'])->name('password.reset');//done
    Route::post('reset', [DoctorAuthController::class, 'reset'])->name('password.update');//done

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [DoctorAuthController::class, 'logout']);//done
        Route::get('profile', [DoctorAuthController::class, 'profile']); //done
        Route::post('updatePassword', [DoctorAuthController::class, 'updatePassword']); //done
        Route::delete('/account/delete', [DoctorAuthController::class, 'delete']);//done
        Route::post('/account/update', [DoctorAuthController::class, 'updateProfile']); //done
        Route::get('users/viewRequests', [DoctorController::class, 'view_request']);//done
        Route::get('users/reviewAnswer',[DoctorController::class, 'get_answer']);
        Route::post('users/acceptRequest', [DoctorController::class, 'accept_request']);//done
        Route::post('users/cancelRequest', [DoctorController::class, 'cancel_request']);//done
        Route::get('users/paymentRequests', [DoctorController::class, 'view_payment_requests']);//done
        Route::post('diets/addNewDiet', [DoctorController::class, 'create_diet']);//done
        Route::post('diets/addNewDay', [DoctorController::class, 'add_diet_day']);//done
        Route::get('users/myPatients', [DoctorController::class, 'my_patient']);
        Route::get('diets/UserFile', [DoctorController::class, 'view_user_file']);//done
        Route::post('diets/askForReview', [DoctorController::class, 'request_review']);
        Route::post('diets/getAnswerreviews', [DoctorController::class, 'get_answer']);
        Route::get('diets/viewDiet',[DoctorController::class,'show_diet']);
        Route::post('chat/send_message', [ChatController::class, 'send']);//done
        Route::get('chat/messages', [ChatController::class, 'getChatMessages']);
        Route::post('image/storeCertificationImage', [ImagesController::class, 'storeCertificationImages']);
        Route::post('image/storeProfilePicture', [ImagesController::class, 'storeProfilePicture']);
    });
});


Route::prefix('admins')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']); //done

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('add_admin', [AdminAuthController::class, 'add_admin']);//done
        Route::post('logout', [AdminAuthController::class, 'logout']); //done
        Route::post('updatePassword', [AdminAuthController::class, 'updatePassword']); //done
        Route::post('allUsers', [AdminController::class, 'getAllUsers']);//done
        Route::post('delete_user', [AdminController::class, 'delete_user']);//done
        Route::post('search/user', [AdminController::class, 'search_user']);//done
        Route::post('search/doctor', [AdminController::class, 'search_doctor']);//done
        Route::get('registrationRequests', [AdminController::class, 'view_registration_requests']);//done
        Route::post('acceptRequest', [AdminController::class, 'accept_request']);//done
        Route::post('rejectRequest', [AdminController::class, 'reject_request']);//done
        Route::post('addToWallet', [AdminController::class, 'addMoneyToUserWallet']);
    });
});
