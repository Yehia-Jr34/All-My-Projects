<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Success response method.
     *
     * @param  mixed  $data
     */
    protected function sendSuccess(string $message = '', $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'code' => $statusCode,
        ], $statusCode);
    }

    /**
     * Error response method.
     *
     * @param  mixed  $data
     */
    protected function sendError(string $message, int $statusCode, $data = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'code' => $statusCode,
        ], $statusCode);
    }
}
