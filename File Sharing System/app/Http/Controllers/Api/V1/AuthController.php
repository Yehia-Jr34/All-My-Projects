<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $tokens = $this->authService->login(
            $request->email,
            $request->password
        );

        return response()->json($tokens);
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $tokens = $this->authService->refresh($request->refresh_token);

        $request->user()->tokens()->delete();

        return response()->json($tokens);
    }

    public function logout(RefreshTokenRequest $request): JsonResponse
    {
        $this->authService->logout($request->refresh_token);

        return response()->json(['message' => 'Successfully logged out']);
    }
}
