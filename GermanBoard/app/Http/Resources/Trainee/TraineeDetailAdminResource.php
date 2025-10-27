<?php

namespace App\Http\Resources\Trainee;

use App\Enum\ProvidersMediaEnum;
use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraineeDetailAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'trainee_id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'country' => $this->country,
            'address' => $this->address,
            'email' => $this->user->email,
            'created_at' => $this->created_at,
            'trainings' => $this->trainings->map(function ($training) {
                $certificate = $training->training_trainees()->where('trainee_id' ,$this->id)->first()?->certificate;
                return [
                    'training_id' => $training->id,
                    'title_en' => $training->title_en,
                    'title_ar' => $training->title_ar,
                    'title_du' => $training->title_du,
                    'training_type' => $training->type,
                    'training_cover' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
                    'training_provider_first_name' => $training->provider->first_name,
                    'training_provider_last_name' => $training->provider->last_name,
                    'training_provider_image' => $training->provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
                    'training_provider_specialized_at' => $training->provider->specialized_at,
                    'training_provider_email' => $training->provider->user->email,
                    'passed_the_training' => $training->pivot->passed_the_training,
                    'certification_code' =>  $certificate?->certification_code,
                    'certification_image' => url($certificate?->certification_image),
                    'certification_attached_at' => $certificate?->certification_attached_at,
                    'remaining_hours' => $training->pivot->remaining_hours,
                    'created_at' => $training->pivot->created_at,

                ];
            }),
        ];
    }
}
