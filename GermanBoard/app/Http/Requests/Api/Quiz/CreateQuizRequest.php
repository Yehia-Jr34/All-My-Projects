<?php

namespace App\Http\Requests\Api\Quiz;

use App\Http\Requests\BaseRequest;

class CreateQuizRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => 'required|exists:trainings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'video_id' => 'nullable|exists:videos,id',
            'QA' => 'required|array|min:1',
            'QA.*.question' => 'required|string|max:1000',
            'QA.*.answers' => 'required|array|min:1',
            'QA.*.answers.*.answer_text' => 'required|string|max:1000',
            'QA.*.answers.*.is_correct' => 'required|boolean',
        ];
    }
}
