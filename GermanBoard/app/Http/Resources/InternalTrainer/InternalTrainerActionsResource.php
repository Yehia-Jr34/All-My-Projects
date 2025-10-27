<?php

namespace App\Http\Resources\InternalTrainer;

use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternalTrainerActionsResource extends JsonResource
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
            'internal_trainer_id' => $this->internal_trainer_id,
            'action' => $this->action,
            'training' => $this->whenLoaded('training', function () {
                return [
                    'id' => $this->training_id,
                    'title_en' => $this->training->title_en,
                    'type' =>  $this->training->type
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
