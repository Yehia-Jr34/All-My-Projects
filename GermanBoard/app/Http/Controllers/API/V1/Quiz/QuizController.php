<?php

namespace App\Http\Controllers\API\V1\Quiz;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Quiz\CreateQuizRequest;
use App\Http\Requests\Api\Quiz\SubmitQuizRequest;
use App\Http\Resources\Exam\QuizForTraineeResource;
use App\Http\Resources\Exam\QuizResource;
use App\Services\Quizzes\QuizService;
use Illuminate\Http\JsonResponse;

class QuizController extends BaseApiController
{
    public function __construct(
        private QuizService $quizService
    ) {}

    public function create(CreateQuizRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->quizService->create($data);
        return $this->sendSuccess('Quiz created successfully.', $response, StatusCodeEnum::CREATED->value);
    }
    public function show(int $quiz_id): JsonResponse
    {
        $data = $this->quizService->getQuiz($quiz_id);

        return $this->sendSuccess('Quiz fetched successfully', QuizResource::make($data));
    }

    public function showForTrainee(int $quiz_id): JsonResponse
    {
        $data = $this->quizService->getQuizForTrainee($quiz_id);

        return $this->sendSuccess('Quiz fetched successfully', QuizForTraineeResource::make($data));
    }

    public function submit(SubmitQuizRequest $request): JsonResponse
    {
        $data = $this->quizService->submitQuiz($request->validated());

        return $this->sendSuccess('Quiz fetched successfully', ($data));
    }
}
