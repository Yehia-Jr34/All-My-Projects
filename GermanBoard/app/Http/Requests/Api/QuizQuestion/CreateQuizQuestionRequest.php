<?php

namespace App\Http\Requests\Api\QuizQuestion;

use App\Http\Requests\BaseRequest;

class CreateQuizQuestionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string|max:1000',
            'answers' => 'required|array|min:1',
            'answers.*.answer_text' => 'required|string|max:1000',
            'answers.*.is_correct' => 'required|boolean',
        ];
    }
}
