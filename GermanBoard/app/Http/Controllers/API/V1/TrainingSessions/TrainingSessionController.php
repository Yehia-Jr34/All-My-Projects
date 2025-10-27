<?php

namespace App\Http\Controllers\API\V1\TrainingSessions;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\TrainingSession\CreateSessionRequest;
use App\Http\Requests\Api\TrainingSession\TrainingSessionRequest;
use App\Services\TrainingSession\TrainingSessionService;
use Illuminate\Http\JsonResponse;

class TrainingSessionController extends BaseApiController
{
    public function __construct(
        private TrainingSessionService $trainingSessionService
    ) {}

    public function createSession(CreateSessionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingSessionService->createSession($data);

        return $this->sendSuccess('Training session created successfully.', $response, 201);
    }


}
