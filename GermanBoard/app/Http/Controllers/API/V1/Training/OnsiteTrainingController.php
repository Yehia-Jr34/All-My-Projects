<?php

namespace App\Http\Controllers\API\V1\Training;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\Training\LiveTrainingCardResource;
use App\Http\Resources\Training\OnsiteTrainingDetailsResource;
use App\Services\Training\TrainingService;
use Illuminate\Http\JsonResponse;


class OnsiteTrainingController extends BaseApiController
{
    public function __construct(
        private readonly TrainingService $trainingService,
    )
    {
    }
    public function show($id): JsonResponse
    {
        $trainings = $this->trainingService->getOnsiteTrainingDetail($id);

        return $this->sendSuccess('Training retrieved successfully', OnsiteTrainingDetailsResource::make($trainings), StatusCodeEnum::OK->value);
    }

    public function index(): JsonResponse
    {
        $response = $this->trainingService->getOnsiteTraining();

        $trainings = LiveTrainingCardResource::collection($response['all_onsite_trainings']);
        $ongoingTrainings = LiveTrainingCardResource::collection($response['ongoing_onsite_trainings']);
        $completedTrainings = LiveTrainingCardResource::collection($response['completed_onsite_trainings']);
        $notStartedTrainings = LiveTrainingCardResource::collection($response['not_started_onsite_trainings']);

        return $this->sendSuccess('Onsite trainings fetched successfully', [
            'trainings' => $trainings,
            'ongoingTrainings' => $ongoingTrainings,
            'completedTrainings' => $completedTrainings,
            'notStartedTrainings' => $notStartedTrainings,
        ], 200);
    }

}
