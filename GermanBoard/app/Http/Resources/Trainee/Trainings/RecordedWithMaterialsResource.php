<?php

namespace App\Http\Resources\Trainee\Trainings;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Http\Resources\Attachments\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordedWithMaterialsResource extends JsonResource
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
            'id' => $this['training']->id,
            'title' => $this['training']->$titleField,
            'image' => $this['training']->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'attachments' => AttachmentResource::collection($this['training']->getMedia(TrainingMediaCollectionsEnum::ATTACHMENTS->value)),
            'items' => $this["items"],
            'certification_image' => $this['certification_image'] ? url($this['certification_image']) : null,
            'certification_code' => $this['certification_code'],
        ];
    }
}
