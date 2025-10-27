<?php

namespace App\Http\Controllers\Api\Notification;

use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    public function get(): JsonResponse
    {
        try {
            $data = $this->notificationService->get();
            return $this->sendSuccess("success", $data, StatusCodeEnum::OK);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }
}
