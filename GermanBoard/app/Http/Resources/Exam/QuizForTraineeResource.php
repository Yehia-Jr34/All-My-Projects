<?php

namespace App\Http\Resources\Exam;

use App\Http\Resources\Exam\QuestionResource;
use App\Http\Resources\Training\VideoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizForTraineeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'passing_score' => $this->passing_score,
            'video_id' => $this->video->id,
            'questions' => collect($this->questions)->map(function ($question) {
                return QuestionResource::make($question);
            }),

        ];
    }
}
