<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\KeyObjective\KeyObjectiveResource;
use App\Http\Resources\Provider\ProviderResource;
use App\Http\Resources\Tags\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListTrainingsResource extends JsonResource
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
        $aboutField = 'about_' . $locale;

        $categories = $this->whenLoaded('training_categories', function () {
            return $this->training_categories?->map(function ($category) {
                return $category->category;
            });
        });

        $provider = $this->whenLoaded('provider', function () {
            return ProviderResource::make($this->provider);
        });

        return [
            'id' => $this->id,
            'title' => $this->$titleField,
            'about' => $this->$aboutField,
            'price' => $this->price,
            'type' => $this->type,
            'provider_id' => $this->provider_id,
            'rate' => $this->rate,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration_in_hours' => $this->duration_in_hours,
            'language' => $this->language,
            'number_of_rates' => $this->whenLoaded('training_rates', function () {
                return $this->training_rates->count();
            }, 0),
            'cover' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'provider' => ProviderResource::make($provider),
            'categories' => CategoryResource::collection($categories),
            'training_site' => $this->training_site ?: "",
        ];
    }
}
