<?php

namespace App\Http\Resources\Sessions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'date' => \Carbon\Carbon::parse($this->start_date)->toDateString(), // e.g. "2023-10-01"
            'time' => \Carbon\Carbon::parse($this->start_date)->format('H:i'),  // e.g. "12:00"
            'status' => $this->status,
            'title' => $this->title,
            'notes' => $this->notes,
            'meet_url' => $this->meet_url
        ];
    }
}
