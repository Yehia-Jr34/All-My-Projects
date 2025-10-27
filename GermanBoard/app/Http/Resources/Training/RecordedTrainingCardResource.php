<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordedTrainingCardResource extends JsonResource
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
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title_du' => $this->title_du,
            'cover_image' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'created_at' => $this->created_at,
        ];
    }
}
