<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Auth\AskForOTPRequest;
use App\Http\Requests\Api\Auth\CheckCodeRequest;
use App\Http\Requests\Api\Auth\CheckEmailExistenceRequest;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\RegisterWithGoogleRequest;
use App\Http\Requests\Api\Auth\ResendForgetPasswordCodeRequest;
use App\Http\Requests\Api\Auth\ResendOTPRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\VerifyAccountRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseApiController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function askForOTP(AskForOTPRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->askForOTP($data);

        return $this->sendSuccess('OTP has been sent.', $response, 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->authService->register($data);

        return $this->sendSuccess('user registered successfully!, please verify your email to activate your account.', $response, 201);
    }

    public function verifyAccount(VerifyAccountRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->authService->verifyAccount($data);

        return $this->sendSuccess('user verified successfully!', [], 200);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credential = $request->validated();

        $data = $this->authService->login($credential);

        return $this->sendSuccess('user logged in successfully!', $data, 200);
    }

    public function provider_login(LoginRequest $request): JsonResponse
    {
        $credential = $request->validated();

        $data = $this->authService->provider_login($credential);

        return $this->sendSuccess('user logged in successfully!', $data, 200);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->sendSuccess('user logged out successfully!', [], 200);
    }

    public function forgetPasswordForTrainee(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->authService->forgetPasswordForTrainee($data);

        return $this->sendSuccess('password reset code has sent successfully', $response, 200);
    }

    public function resendForgetPasswordCode(ResendForgetPasswordCodeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->resendForgetPasswordCode($data);

        return $this->sendSuccess('password reset code has sent successfully', $response, 200);
    }

    public function checkCodeForTrainee(CheckCodeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->checkCodeForTrainee($data);

        return $this->sendSuccess('code has checked successfully', $response, 200);
    }

    public function forgetPasswordForProvider(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->authService->forgetPasswordForProvider($data);

        return $this->sendSuccess('password reset code has sent successfully', $response, 200);
    }

    public function checkCodeForProvider(CheckCodeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->checkCodeForProvider($data);

        return $this->sendSuccess('code has checked successfully', $response, 200);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $this->authService->resetPassword($data);

        return $this->sendSuccess('password reset successfully!', [], 200);
    }

    public function resendVerificationCode(ResendOTPRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->resendOTP($data);

        return $this->sendSuccess('resend otp successfully!', $response, 200);
    }

    public function profile(): JsonResponse
    {
        $response = $this->authService->profile();

        return $this->sendSuccess('user profile', $response, 200);
    }

    public function checkEmailExistence(CheckEmailExistenceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->checkEmailExistence($data);
        return $this->sendSuccess('email has checked successfully', $response, 200);
    }

    public function register_with_google(RegisterWithGoogleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->authService->register_with_google($data);

        return $this->sendSuccess('user registered successfully!', $response, 201);
    }
}
