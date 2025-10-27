<?php

namespace App\Http\Controllers\API\V1\Training;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;

use App\Http\Resources\Training\TrainingDetailsResource;
use App\Services\Training\TrainingService;
use Illuminate\Http\JsonResponse;
use function Pest\Laravel\json;


class LiveTrainingController extends BaseApiController
{
    public function __construct(
        private readonly TrainingService $trainingService,
    ) {}
    public function show($id): JsonResponse
    {
        $trainings = $this->trainingService->getLiveTraining($id);

        return $this->sendSuccess('Training retrieved successfully', TrainingDetailsResource::make($trainings), StatusCodeEnum::OK->value);
    }
}
