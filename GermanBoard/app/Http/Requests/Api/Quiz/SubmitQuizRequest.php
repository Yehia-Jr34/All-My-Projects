<?php

namespace App\Http\Requests\Api\Quiz;

use App\Http\Requests\BaseRequest;

class SubmitQuizRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            '*.question_id' => 'required|numeric|exists:questions,id',
            '*.answer_id' => 'required|numeric|exists:answers,id'
        ];
    }
}
