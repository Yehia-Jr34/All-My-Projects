<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\KeyObjective\KeyObjectiveResource;
use App\Http\Resources\Provider\ProviderResource;
use App\Http\Resources\Tags\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetTrainingResource extends JsonResource
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
        $tags = $this->whenLoaded('training_tags', function () {
            return $this->training_tags?->map(function ($tag) {
                return $tag->tags;
            });
        });
        $keyLearningObjectives = $this->whenLoaded('key_learning_objectives', function () {
            return $this->key_learning_objectives;
        });
        $categories = $this->whenLoaded('training_categories', function () {
            return $this->training_categories?->map(function ($category) {
                return $category->category;
            });
        });
        $provider = $this->whenLoaded('provider', function () {
            return ProviderResource::make($this->provider);
        });

        $videos =  $this->whenLoaded('videos', function ($videos){
            return $videos->map(function ($video){
                return [
                    'id' => $video->id,
                    'title'=>$video->title,
                    'description'=>$video->description,
                    'duration' => 'not determined',
                ];
            });

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
            'tags' => TagResource::collection($tags),
            'key_learning_objectives' => KeyObjectiveResource::collection($keyLearningObjectives),
            'provider' => ProviderResource::make($provider),
            'categories' => CategoryResource::collection($categories),
            'training_site' => $this->training_site ?: "",
            'videos' => $videos
        ];
    }
}
