<?php

namespace App\Http\Controllers\API\V1\InternalTrainer;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\InternalTrainer\AssignTrainingRequest;
use App\Http\Requests\InternalTrainer\StoreInternalTrainerRequest;
use App\Http\Resources\InternalTrainer\InternalTrainerTrainingsResource;
use App\Http\Resources\InternalTrainer\ProviderInternalTrainersResource;
use App\Services\InternalTrainer\InternalTrainerService;
use Illuminate\Http\JsonResponse;

class InternalTrainerTrainingController extends BaseApiController
{
    public function __construct(
        private InternalTrainerService $internalTrainerService
    )
    {
    }

    public function assign(AssignTrainingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->internalTrainerService->assignTraining($data);

        return $this->sendSuccess('Assigning successfully.',[], StatusCodeEnum::CREATED->value);
    }

    public function index(int $internal_trainer_id): JsonResponse
    {

        $data = $this->internalTrainerService->getInternalTrainerTrainings($internal_trainer_id);

        return $this->sendSuccess('Fetched successfully.',InternalTrainerTrainingsResource::make($data), StatusCodeEnum::OK->value);
    }

}
