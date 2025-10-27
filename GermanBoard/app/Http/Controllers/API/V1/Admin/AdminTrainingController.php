<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Services\Training\TrainingService;
use Illuminate\Http\JsonResponse;

class AdminTrainingController extends BaseApiController
{
    public function __construct(
        private readonly TrainingService $trainingService
    ) {}

    public function isAdminTraining($training_id): JsonResponse
    {
        $is_admin_training = $this->trainingService->isAdminTraining($training_id);

        return $this->sendSuccess('fetched successfully', $is_admin_training, StatusCodeEnum::OK->value);
    }
}
