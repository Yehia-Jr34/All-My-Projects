<?php

namespace App\Http\Resources\Provider;


use App\Enum\ProvidersMediaEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
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
            'name' => $this->nickname,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'brief' => $this->brief,
            'specialized_at' => $this->specialized_at,
            'photo' => $this->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
            'role' => $this->user->roles()->first()->name,

        ];
    }


}
