<?php

namespace App\Http\Resources\Certificates;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateByCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'certification_code' => $this?->certification_code ?? null,
            'certification_image' => isset($this->certification_image)
                ? url($this->certification_image)
                : null,
            'certification_attached_at' => $this?->certification_attached_at ?? null,
        ];
    }
}
