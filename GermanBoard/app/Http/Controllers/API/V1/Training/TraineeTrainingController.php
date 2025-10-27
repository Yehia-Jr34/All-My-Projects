<?php

namespace App\Http\Controllers\API\V1\Training;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\Trainee\Trainings\TraineeTrainingsResource;
use App\Services\Trainee\TraineeService;
use Illuminate\Http\JsonResponse;


class TraineeTrainingController extends BaseApiController
{
    public function __construct(
        private readonly TraineeService $traineeService,
    )
    {
    }
    public function index(): JsonResponse
    {
        $trainings = $this->traineeService->getMyTrainings();

        return $this->sendSuccess('Training retrieved successfully', TraineeTrainingsResource::make($trainings), StatusCodeEnum::OK->value);
    }
}
