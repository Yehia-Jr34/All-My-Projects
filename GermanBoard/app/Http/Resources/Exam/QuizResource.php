<?php

namespace App\Http\Resources\Exam;

use App\Http\Resources\Training\VideoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $video = VideoResource::make($this->video);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'passing_score' => $this->passing_score,
            'video' => $video,
            'questions' => collect($this->questions)->map(function ($question) {
                return QuestionResource::make($question);
            }),

        ];
    }
}
