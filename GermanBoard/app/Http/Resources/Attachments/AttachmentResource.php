<?php

namespace App\Http\Resources\Attachments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'file_url' => $this->getUrl(),
            'file_name' => $this->file_name,
            'file_size' => round(($this->size ?? 0) / (1024), 2).'.KB',
            'file_date' => \Carbon\Carbon::parse($this->created_at->format('Y-m-d H:i:s'))->toDateString(),
        ];
    }
}
