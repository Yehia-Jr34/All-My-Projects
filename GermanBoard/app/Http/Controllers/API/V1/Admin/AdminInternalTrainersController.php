<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Services\InternalTrainer\InternalTrainerService;
use Illuminate\Http\JsonResponse;

class AdminInternalTrainersController extends BaseApiController
{
    public function __construct(
        private readonly InternalTrainerService $internalTrainerService
    ) {}

    public function isAdminInternalTrainer($internal_trainer_id): JsonResponse
    {
        $is_admin_internal_trainer = $this->internalTrainerService->isAdminInternalTrainer($internal_trainer_id);

        return $this->sendSuccess('fetched successfully', $is_admin_internal_trainer, StatusCodeEnum::OK->value);
    }
}
