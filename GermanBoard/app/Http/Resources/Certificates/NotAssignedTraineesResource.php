<?php

namespace App\Http\Resources\Certificates;

use App\Enum\ProvidersMediaEnum;
use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotAssignedTraineesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trainee_name' => $this->trainee->first_name . ' ' . $this->trainee->last_name,
            'trainee_email' => $this->trainee->user->email,
            'training_id' => $this->training->id,
            'training_title_en' => $this->training->title_en,
            'training_title_ar' => $this->training->title_ar,
            'training_title_du' => $this->training->title_du,
            'training_cover' => $this->training->getMedia(TrainingMediaCollectionsEnum::COVERS->value)?->first()?->getUrl(),
            'training_type' => $this->training->type,
            'passed_the_training' => $this->passed_the_training,
            'trainee_id' => $this->trainee->id,
            'provider' => [
                'id' => $this->training->provider->id,
                'name' => $this->training->provider->first_name . ' ' . $this->training->provider->last_name,
                'email' => $this->training->provider->user->email,
                'provider_image' => $this->training->provider->getMedia(ProvidersMediaEnum::PHOTO->value)?->first()?->getUrl(),
            ],

        ];
    }
}
