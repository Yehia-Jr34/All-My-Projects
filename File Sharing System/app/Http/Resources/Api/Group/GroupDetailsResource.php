<?php

namespace App\Http\Resources\Api\Group;

use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupDetailsResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'owner_id' => $this->owner->id,
            'owner_name' => $this->owner->name,
            'owner_email' => $this->owner->email,
            'owner_username' => $this->owner->username,
            'users' => UserResource::collection($this->users)
        ];
    }
}
