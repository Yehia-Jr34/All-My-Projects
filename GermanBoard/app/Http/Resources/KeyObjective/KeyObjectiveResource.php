<?php

namespace App\Http\Resources\KeyObjective;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KeyObjectiveResource extends JsonResource
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
            'text' => $this->text,
        ];
    }
}
