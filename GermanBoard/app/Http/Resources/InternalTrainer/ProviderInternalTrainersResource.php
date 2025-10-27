<?php

namespace App\Http\Resources\InternalTrainer;

use App\Enum\InternalTrainerMediaCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderInternalTrainersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user_email = $this->whenLoaded('user', function ($user) {
            return $user->email;
        });
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'email' => $user_email,
            'profile_image' => $this->getFirstMediaUrl(InternalTrainerMediaCollection::PROFILE_IMAGE->value),
            'gender' => $this->gender,
            'created_at' => $this->created_at
        ];
    }
}
