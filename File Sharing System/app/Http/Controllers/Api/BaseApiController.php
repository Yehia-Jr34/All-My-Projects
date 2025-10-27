<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class BaseApiController extends Controller
{
    /**
     * Success response method.
     *
     * @param  mixed  $data
     */
    protected function sendSuccess(string $message = '', $data = null, StatusCodeEnum $statusCode = StatusCodeEnum::OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'code' => $statusCode->value,
        ], $statusCode->value);
    }

    /**
     * Error response method.
     *
     * @param  mixed  $data
     */
    protected function sendError(string $message, StatusCodeEnum $statusCode, $data = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode->value,
        ], $statusCode->value);
    }
}
