<?php

namespace App\Http\Requests\Api\QuizQuestion;

use App\Http\Requests\BaseRequest;

class CheckAnswerRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'quiz_id' => 'required|exists:quizzes,id',
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'required|exists:answers,id',
        ];
    }
}
