<?php

namespace App\Http\Resources\Api\Files;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyUnlocksResourc extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'file_name' => $this->file->name,
            'file_id' => $this->file->id,
            'locked_at' =>$this->created_at
        ];
    }
}
