<?php

namespace App\Http\Resources\Training;

use App\Enum\TrainingMediaCollectionsEnum;
use App\Enum\TrainingTypeEnum;
use App\Http\Resources\Attachments\AttachmentResource;
use App\Http\Resources\Provider\ProviderResource;
use App\Http\Resources\Sessions\SessionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingDetailsResource extends JsonResource
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

        return [
            'id' => $this->id,
            'title' => $this->$titleField,
            'about' => $this->$aboutField,
            'price' => $this->price,
            'type' => $this->type,
            'language' => $this->language,
            'rate' => $this->rate,
            'cover' => $this->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
            'number_of_rates' => $this->whenLoaded('training_rates', function () {
                return $this->training_rates->count();
            }, 0),
            'provider' => $this->whenLoaded('provider', function () {
                return ProviderResource::make($this->provider);
            }),
            'sessions' => $this->whenLoaded('sessions', function () {
                return SessionResource::collection($this->sessions);
            }),
            'attachments' => AttachmentResource::collection($this->getMedia(TrainingMediaCollectionsEnum::ATTACHMENTS->value)),
        ];
    }
}
