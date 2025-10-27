<?php

namespace App\Http\Resources\Provider;

use App\Enum\ProvidersMediaEnum;
use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderForTraineeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $titleField = 'title_' . $locale;
        $numberOfTrainee = 0 ;
        return [
            'id' => $this->id,
            'name' =>$this->nickname,
            'phone_number' => $this->work_phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'brief' => $this->brief,
            'location' => $this->location,
            'specialized_at' => $this->specialized_at,
            'photo' => $this->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
            'cover' => $this->getFirstMediaUrl(ProvidersMediaEnum::COVER->value),
            'role' => $this->user->getRoleNames()->first(),
            'trainings' => $this->trainings->map(function($training) use($titleField, &$numberOfTrainee){
                $numberOfTrainee += sizeof($training->training_trainees()->where('payment_status','success')->get());
                return [
                    "id" => $training->id,
                    "name" => $training->$titleField,
                    "cover" => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
                    "rating" => $training->rate,
                    "price" => $training->price,
                    "type" => $training->type,
                    'trainee_enrolled' => sizeof($training->training_trainees)
                ];
            }),
            'all_trainee_enrolled' =>$numberOfTrainee
        ];
    }
}
