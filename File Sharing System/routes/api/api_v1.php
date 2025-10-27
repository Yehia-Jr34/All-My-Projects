<?php

declare(strict_types=1);


use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\File\FileController;
use App\Http\Controllers\Api\Group\GroupController;
use App\Http\Controllers\Api\Invitation\InvitationController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\FileVersion\FileVersionController;
use App\Http\Middleware\GlobalErrorHandler;
use App\Http\Middleware\LogRequestTiming;
use Illuminate\Support\Facades\Route;



// AUTH

Route::middleware([GlobalErrorHandler::class, LogRequestTiming::class])->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/verify', [AuthController::class, 'verifyAccount']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgetPassword/getOTP', [AuthController::class, 'getOTP']);
    Route::post('auth/forgetPassword/checkCode', [AuthController::class, 'checkCode']);
    Route::post('auth/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::middleware(['auth:sanctum', GlobalErrorHandler::class, LogRequestTiming::class])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

    // Users
    Route::get('user/getProfile', [UserController::class, 'getProfile']);
    Route::post('user/updateProfile', [UserController::class, 'update']);
    Route::post('user/filter', [UserController::class, 'filterUsers']);
    Route::get('user/groups', [UserController::class, 'userGroups']);



    // Group
    Route::post('group/create', [GroupController::class, 'create']);
    Route::get('group/files/{id}', [GroupController::class, 'showWithfiles']);
    Route::get('group/{id}', [GroupController::class, 'show']);
    Route::delete('user/group/exit/{group_id}', [GroupController::class, 'exitGroup']);
    Route::post('group/rename', [GroupController::class, 'editGroupName']);
    Route::post('group/remove', [GroupController::class, 'removeMember']);
    Route::post('user/report', [GroupController::class, 'getUserReport']); //test


    // Invitation
    Route::post('invitation/create', [InvitationController::class, 'create']);
    Route::patch('invitation/accept/{id}', [InvitationController::class, 'accept']);
    Route::patch('invitation/reject/{id}', [InvitationController::class, 'reject']);
    Route::get('invitation/getUsersInvitations', [InvitationController::class, 'getInvitations']);

    // File
    Route::post('file/create', [FileController::class, 'create']);
    Route::patch('file/lock', [FileController::class, 'lock']);
    Route::post('file/unlock', [FileController::class, 'unLock']);
    Route::get('group/files/admin/{id}', [FileController::class, 'showWithfilesAdmin']);
    Route::patch('file/accept/{id}', [FileController::class, 'accept']);
    Route::patch('file/reject/{id}', [FileController::class, 'reject']);
    Route::get('file/unlock/me/{group_id}', [FileController::class, 'getUnlocks']);
    Route::post('/file/requests', [FileController::class, 'fileRequests']);
    Route::post('/file/delete', [FileController::class, 'deleteFile']);
    Route::post('/file/tracing', [FileController::class, 'getHistory']);

    Route::get('/file/report/{file_id}', [FileController::class, 'getFileReport']);


    // File Version
    Route::get('file/version/download/{id}', [FileVersionController::class, 'download']);

    //notification
    Route::get('notification', [NotificationController::class, 'get']);

    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
});
