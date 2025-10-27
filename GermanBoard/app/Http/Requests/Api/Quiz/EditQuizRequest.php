<?php

namespace App\Http\Requests\Api\Quiz;

use App\Http\Requests\BaseRequest;

class EditQuizRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'video_id' => 'required|integer|exists:videos,id',
            'passing_score' => 'required|integer|min:0|max:100',
        ];
    }
}
