<?php

namespace App\Http\Resources\Provider;

use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderTrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $recorded = [];
        $live = [];
        $onsite = [];

        foreach ($this->trainings as $training) {
            if ($training->type === 'recorded') {
                $recorded[] = [
                    'id' => $training->id,
                    'title_ar' => $training->title_ar,
                    'title_en' => $training->title_en,
                    'title_du' => $training->title_du,
                    'cover_image' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
                    'created_at' => $training->created_at,
                ];
            } elseif ($training->type === 'live') {
                $live[] = [
                    'id' => $training->id,
                    'title_ar' => $training->title_ar,
                    'title_en' => $training->title_en,
                    'title_du' => $training->title_du,
                    'cover_image' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
                    'created_at' => $training->created_at,
                ];
            } elseif ($training->type === 'onsite') {
                $onsite[] = [
                    'id' => $training->id,
                    'title_ar' => $training->title_ar,
                    'title_en' => $training->title_en,
                    'title_du' => $training->title_du,
                    'cover_image' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
                    'created_at' => $training->created_at,
                ];
            }
        }

        return [
            'recorded' => $recorded,
            'live' => $live,
            'onsite' => $onsite,
        ];
    }
}
