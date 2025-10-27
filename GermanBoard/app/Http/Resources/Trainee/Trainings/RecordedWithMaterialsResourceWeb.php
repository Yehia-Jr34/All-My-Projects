<?php

namespace App\Http\Resources\Trainee\Trainings;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Attachments\AttachmentResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\KeyObjective\KeyObjectiveResource;
use App\Http\Resources\Provider\ProviderResource;
use App\Http\Resources\Tags\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordedWithMaterialsResourceWeb extends JsonResource
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

        $training = $this['training'];

        return [
            'id' => $training->id,
            'title' => $training->$titleField,
            'about' => $training->$aboutField,
            'price' => $training->price,
            'type' => $training->type,
            'provider_id' => $training->provider_id,
            'rate' => $training->rate,
            'duration_in_hours' => $training->duration_in_hours,
            'language' => $training->language,

            'image' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),

            // Tags
            'tags' => TagResource::collection(
                $training->training_tags?->map(fn($tag) => $tag->tags) ?? collect()
            ),

            // Key learning objectives
            'key_learning_objectives' => KeyObjectiveResource::collection(
                $training->key_learning_objectives ?? collect()
            ),

            'number_of_rates' => $training->training_rates->count(),

            // Provider
            'provider' => ProviderResource::make($training->provider),

            // Categories
            'categories' => CategoryResource::collection(
                $training->training_categories?->map(fn($category) => $category->category) ?? collect()
            ),

            // Attachments
            'attachments' => AttachmentResource::collection(
                $training->getMedia(TrainingMediaCollectionsEnum::ATTACHMENTS->value)
            ),

            // Items (already passed in your service)
            'items' => $this['items'],
        ];
    }

}
