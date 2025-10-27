<?php

namespace App\Http\Resources\InternalTrainer;

use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternalTrainerTrainingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $trainings = $this->whenLoaded('trainings' , function ($trainings){return $trainings;});

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'trainings' => InternalTrainerTrainingResource::collection($trainings)
        ];
    }
}
