<?php

namespace App\Http\Resources\Trainee\Trainings;

use App\Enum\TrainingTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraineeTrainingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $recorded = [] ;

        $live = [] ;

        $this->trainings->each(function ($item) use (&$live , &$recorded){
           if($item->type === TrainingTypeEnum::RECORDED->value)
               $recorded[] = $item;
           elseif ($item->type === TrainingTypeEnum::LIVE->value)
               $live[] = $item;
        });

        return [
            'recorded' => RecordedTrainingResource::collection($recorded),
            'live' => LiveTrainingResource::collection($live)
        ];
    }
}
