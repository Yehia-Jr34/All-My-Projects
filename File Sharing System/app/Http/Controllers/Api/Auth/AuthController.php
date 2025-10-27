<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\Auth\UserLoginDTO;
use App\DTOs\Auth\UserRegistrationDTO;
use App\DTOs\Auth\CheckCodeDTO;
use App\DTOs\User\FilteredUserDTO;
use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Auth\CheckCodeRequest;
use App\Http\Requests\Api\Auth\GetOTPRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\LogoutRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\VerifyAcountRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Services\Auth\AuthService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    public function __construct(
        protected AuthService $authService,

    ) {}

    public function register(RegisterRequest $request)
    {
        $userInformation = UserRegistrationDTO::fromArray($request->validated());

        $userDTO = $this->authService->register($userInformation);

        return $this->sendSuccess(
            'registered successfully',
            $userDTO
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $userInformation = UserLoginDTO::fromArray($request->validated());

        $userDTO = $this->authService->login($userInformation);


        if ($userDTO['data'] == null) {
            return $this->sendError(
                $userDTO['message'],
                StatusCodeEnum::UNAUTHORIZED
            );
        } else {
            return $this->sendSuccess(
                $userDTO['message'],
                $userDTO['data'],
                StatusCodeEnum::OK
            );
        }
    }

    public function checkCode(CheckCodeRequest $request)
    {
        $checkCodeDTO = CheckCodeDTO::fromArray($request->validated());

        try {
            $token = $this->authService->checkCode($checkCodeDTO);
            return $this->sendSuccess('verified successfully', [
                "token" => $token
            ]);
        } catch (DomainException $e) {
            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

    public function logout(LogoutRequest $request)
    {
        Auth::guard('sanctum')->user()->tokens()->delete();
        return $this->sendSuccess('Logged out', []);
    }

    public function getOTP(GetOTPRequest $request)
    {
        $tmp = $this->authService->sendCode($request->email);
        if ($tmp) {
            return $this->sendSuccess('Code sent', []);
        }
        return $this->sendError('An error occurred', StatusCodeEnum::BAD_REQUEST);
    }

    public function verifyAccount(VerifyAcountRequest $request): JsonResponse
    {
        $userVerifyDTO = CheckCodeDTO::fromArray($request->validated());

        try {
            $tokens = $this->authService->verifyAccount($userVerifyDTO);

            return $this->sendSuccess('verified successfully', $tokens);
        } catch (DomainException $e) {

            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword($request->password);
        return $this->sendSuccess('password updated successfully', []);
    }

    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $tokens = $this->authService->refresh($request->refresh_token);

        return $this->sendSuccess('token refreshed successfully', $tokens);
    }
}
