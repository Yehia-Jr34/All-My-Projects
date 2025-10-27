<?php

namespace App\Http\Controllers\API\V1\Training;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\Trainee\Trainings\RecordedWithMaterialsResourceWeb;
use App\Http\Resources\Training\GetRecordedTrainingResource;
use App\Http\Resources\Training\RecordedTrainingCardResource;
use App\Http\Resources\Training\RecordedWithQuizzesResource;
use App\Services\Training\TrainingService;
use Illuminate\Http\JsonResponse;
use function Pest\Laravel\json;


class RecordedTrainingController extends BaseApiController
{
    public function __construct(
        private readonly TrainingService $trainingService,
    ) {}
    public function show($id): JsonResponse
    {
        $trainings = $this->trainingService->getRecordedTraining($id);

        return $this->sendSuccess('Training retrieved successfully', GetRecordedTrainingResource::make($trainings), StatusCodeEnum::OK->value);
    }

    public function showWithQuizzes(int $training_id)
    {
        $training = $this->trainingService->getRecordedTrainingDetails($training_id);

        $response = RecordedWithQuizzesResource::make($training);

        return $this->sendSuccess('Training details retrieved successfully', $response);
    }

    public function index(): JsonResponse
    {

        $trainings = $this->trainingService->getRecordedTrainings();

        return $this->sendSuccess('Training details retrieved successfully', RecordedTrainingCardResource::collection($trainings));
    }

    public function showTraineeTraining($id): JsonResponse
    {
        $trainings = $this->trainingService->getVideosAndExams($id);

        return $this->sendSuccess('Training retrieved successfully', RecordedWithMaterialsResourceWeb::make($trainings), StatusCodeEnum::OK->value);
    }
}
