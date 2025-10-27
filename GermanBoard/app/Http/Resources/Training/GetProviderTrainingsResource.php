<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetProviderTrainingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $recorded = [];
        $live = [];
        $onsite = [];

        foreach ($this->collection as $training) {
            if ($training->type === 'recorded') {
                $recorded[] = $training;
            } elseif ($training->type === 'live') {
                $live[] = $training;
            } elseif ($training->type === 'onsite') {
                $onsite[] = $training;
            }
        }

        return [
            'recorded' => $recorded,
            'live' => $live,
            'onsite' => $onsite,
        ];
    }
}
