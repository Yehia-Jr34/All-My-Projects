<?php

namespace App\Http\Controllers\API\V1\Quiz\Question;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Quiz\CreateQuizRequest;
use App\Http\Requests\Api\QuizQuestion\CheckAnswerRequest;
use App\Http\Requests\Api\QuizQuestion\CreateQuizQuestionRequest;
use App\Http\Requests\Api\QuizQuestion\EditQuizQuestionRequest;
use App\Services\QuizQuestion\QuizQuestion;
use Illuminate\Http\JsonResponse;

class QuizQuestionController extends BaseApiController
{
    public function __construct(
        private QuizQuestion $quizQuestion
    ) {}

    public function create(CreateQuizQuestionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->quizQuestion->create($data);

        return $this->sendSuccess('Quiz question created successfully.', $response, StatusCodeEnum::CREATED->value);
    }

    public function edit(EditQuizQuestionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->quizQuestion->edit($data);

        return $this->sendSuccess('Quiz question edited successfully.', $response, StatusCodeEnum::OK->value);
    }

    public function checkAnswer(CheckAnswerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->quizQuestion->checkAnswer($data);

        return $this->sendSuccess('Quiz question fetched successfully.', $response, StatusCodeEnum::OK->value);
    }
}
