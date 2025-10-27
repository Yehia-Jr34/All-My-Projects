<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Attachments\AttachmentResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\KeyObjective\KeyObjectiveResource;
use App\Http\Resources\Sessions\SessionResource;
use App\Http\Resources\Tags\TagResource;
use App\Http\Resources\Trainee\TraineeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveTrainingDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sessions = $this->whenLoaded('sessions', function () {
            return $this->sessions;
        });

        $keyLearningObjectives = $this->whenLoaded('key_learning_objectives', function () {
            return $this->key_learning_objectives;
        });

        $trainingCategories = $this->whenLoaded('training_categories', function () {
            return $this->training_categories->map(function ($category) {
                return $category->category;
            });
        });

        $trainingRates = $this->whenLoaded('training_rates', function () {
            return $this->training_rates;
        });

        $trainingTrainees = $this->whenLoaded('training_trainees', function () {
            return $this->training_trainees->map(function ($trainee) {
                return $trainee;
            });
        });

        $tags = $this->whenLoaded('training_tags', function () {
            return $this->training_tags->map(function ($tag) {
                return $tag->tags;
            });
        });

        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title_du' => $this->title_du,
            'about_ar' => $this->about_ar,
            'about_en' => $this->about_en,
            'about_du' => $this->about_du,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration_in_hours' => $this->duration_in_hours,
            'price' => $this->price,
            'type' => $this->type,
            'rate' => $this->rate,
            'language' => $this->language,
            'cover_image' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'provider_id' => $this->provider_id,
            'trainingRates' => $trainingRates->count(),
            'trainingTrainees' => TraineeResource::collection($trainingTrainees),
            'attachments' => AttachmentResource::collection($this->getMedia(TrainingMediaCollectionsEnum::ATTACHMENTS->value)),
            'sessions' => SessionResource::collection($sessions),
            'tags' => TagResource::collection($tags),
            'key_learning_objectives' => KeyObjectiveResource::collection($keyLearningObjectives),
            'categories' => CategoryResource::collection($trainingCategories),
        ];
    }
}
