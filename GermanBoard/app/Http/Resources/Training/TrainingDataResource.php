<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Enum\TrainingTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingDataResource extends JsonResource
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

        return [
            'id' => $this->id,
            'title' => $this->$titleField,
            'cover' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'rate' => $this->rate,
            'duration' => $this->duration_in_hours,
            function () {
                if ($this->type == TrainingTypeEnum::RECORDED->value) {
                    return [
                        'achievementRate' => $this->achievement_percentage,
                        'remainingHours' => "",
                    ];
                }
            },
        ];

    }
}
