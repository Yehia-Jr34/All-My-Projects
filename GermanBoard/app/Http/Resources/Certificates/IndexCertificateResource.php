<?php

namespace App\Http\Resources\Certificates;

use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexCertificateResource extends JsonResource
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
            'certification_code' => $this->certification_code,
            'certification_image' => url($this->certification_image),
            'trainee_name' => $this->training_trainee?->trainee->first_name . ' ' . $this->training_trainee?->trainee->last_name,
            'trainee_email' => $this->training_trainee?->trainee->user->email,
            'training_id' => $this->training_trainee?->training->id,
            'training_title_en' => $this->training_trainee?->training->title_en,
            'training_title_ar' => $this->training_trainee?->training->title_ar,
            'training_title_du' => $this->training_trainee?->training->title_du,
            'training_cover' => $this->training_trainee?->training->getMedia(TrainingMediaCollectionsEnum::COVERS->value)->first()->getUrl(),
            'training_type' => $this->training_trainee?->training->type,
            'certification_attached_at' => $this->certification_attached_at,
            'trainee_id' => $this->training_trainee?->trainee->id,
        ];
    }
}
