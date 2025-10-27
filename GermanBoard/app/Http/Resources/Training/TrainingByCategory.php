<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingByCategory extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $titleField = 'title_' . $locale;
        return [
            "id" => $this->id,
            "name" => $this->$titleField,
            "cover" => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            "rating" => $this->rate,
            "price" => $this->price,
            "type" => $this->type,
            "provider" => $this->whenLoaded('provider'),
        ];
    }
}
