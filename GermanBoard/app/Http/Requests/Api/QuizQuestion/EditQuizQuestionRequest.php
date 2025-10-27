<?php

namespace App\Http\Requests\Api\QuizQuestion;

use App\Http\Requests\BaseRequest;

class EditQuizQuestionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:questions,id',
            'question' => 'string|max:1000',
            'answers' => 'array|min:1',
            'answers.*.id' => 'exists:answers,id',
            'answers.*.answer_text' => 'string|max:1000',
            'answers.*.is_correct' => 'boolean',
        ];
    }
}
