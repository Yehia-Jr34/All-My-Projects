<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Attachments\AttachmentResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\KeyObjective\KeyObjectiveResource;
use App\Http\Resources\Provider\ProviderResource;
use App\Http\Resources\Tags\TagResource;
use App\Http\Resources\Trainee\TraineeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordedWithQuizzesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tags = $this->whenLoaded('training_tags', function () {
            return $this->training_tags?->map(function ($tag) {
                return $tag->tags;
            });
        });

        $categories = $this->whenLoaded('training_categories', function () {
            return $this->training_categories?->map(function ($category) {
                return $category->category;
            });
        });

        $keyLearningObjectives = $this->whenLoaded('key_learning_objectives', function () {
            return $this->key_learning_objectives;
        });

        $videos = $this->whenLoaded('videos', function ($videos) {
            return $videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'type' => 'video',
                    'src' => url("storage/$video->src")
                ];
            });
        });

        $trainingTrainees = $this->whenLoaded('training_trainees', function () {
            return $this->training_trainees?->map(function ($trainee) {
                return $trainee;
            });
        });

        $quizzes = $this->whenLoaded('quizzes', function ($quizzes) {
            return $quizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'video_id' => $quiz->video_id,
                    'type' => 'quiz'
                ];
            });
        });

        $merged = collect();

        foreach ($videos as $video) {
            $merged->push($video);
            $relatedQuizzes = $quizzes->where('video_id', $video['id']);
            $merged = $merged->merge($relatedQuizzes);
        }


        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title_du' => $this->title_du,
            'about_ar' => $this->about_ar,
            'about_en' => $this->about_en,
            'about_du' => $this->about_du,
            'price' => $this->price,
            'type' => $this->type,
            'provider_id' => $this->provider_id,
            'rate' => $this->rate,
            'number_of_rates' => $this->whenLoaded('training_rates', function () {
                return $this->training_rates->count();
            }, 0),
            'duration_in_hours' => $this->duration_in_hours,
            'language' => $this->language,
            'cover' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'tags' => TagResource::collection($tags),
            'categories' => CategoryResource::collection($categories),
            'attachments' => AttachmentResource::collection($this->getMedia(TrainingMediaCollectionsEnum::ATTACHMENTS->value)),
            'key_learning_objectives' => $keyLearningObjectives,
            'trainingTrainees' => TraineeResource::collection($trainingTrainees),
            'items' => $merged
        ];
    }
}
