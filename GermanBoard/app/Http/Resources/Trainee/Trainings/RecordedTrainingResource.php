<?php

namespace App\Http\Resources\Trainee\Trainings;

use App\Enum\ProvidersMediaEnum;
use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordedTrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $titleField = 'title_'.$locale;
        $provider = $this->whenLoaded('provider',function ($provider){
            return $provider;
        });
        return [
            'id' => $this->id,
            'courseTitle' => $this->$titleField,
            'courseImage' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'rating' => $this->rate,
            'duration_in_hours' => $this->duration_in_hours,
            'instructorName' => $provider?->nickname ?? "no name",
            'instructorRole' => $provider->specialized_at ?? "UnKnown",
            'specialized' => $provider->specialized_at ?? "UnKnown",
            'providerRole' => $provider->user->getRoleNames()->join(', ') ?? "UnKnown",
            'instructorImage' =>  $provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
            'isCompleted' => (bool)$this->pivot->passed_the_training,
            'hoursRemaining' => $this->pivot->remaining_hours,
            'achievementRate' => $this->pivot->achievement_percentage
        ];
    }
}
