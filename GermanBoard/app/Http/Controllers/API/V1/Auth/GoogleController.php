<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseApiController;
use App\Services\Auth\GoogleService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleController extends BaseApiController
{
    public function __construct(
        private readonly GoogleService $googleService,
    ) {}

    public function redirectToGoogle(): RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        return $this->googleService->redirectToGoogle();
    }

    public function handleGoogleCallback(): JsonResponse
    {
        $response = $this->googleService->handleGoogleCallback();

        return $this->sendSuccess('success', $response, 200);
    }
}
