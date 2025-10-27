<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Attachments\AttachmentResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\KeyObjective\KeyObjectiveResource;
use App\Http\Resources\Provider\ProviderResource;
use App\Http\Resources\Tags\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordedTrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $titleField = 'title_'.$locale;
        $aboutField = 'about_'.$locale;
        $tags = $this->whenLoaded('training_tags', function () {
            return $this->training_tags?->map(function ($tag) {
                return $tag->tags;
            });
        });
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
            'number_of_rates' => $this->whenLoaded('training_rates', function () {
                return $this->training_rates->count();
            }, 0),
            'cover' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'tags' => TagResource::collection($tags),
            'provider' => ProviderResource::make($provider),
            'categories' => CategoryResource::collection($categories),
            'attachments' => AttachmentResource::collection($this->getMedia(TrainingMediaCollectionsEnum::ATTACHMENTS->value)),
            'certificates' => 'certificates',
        ];
    }
}

