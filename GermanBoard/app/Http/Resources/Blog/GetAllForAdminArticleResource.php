<?php

namespace App\Http\Resources\Blog;

use App\Enum\BlogMediaCollectionEnum;
use App\Enum\ProvidersMediaEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAllForAdminArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->getFirstMediaUrl(BlogMediaCollectionEnum::Image->value),
            'status' => $this->status,
            'reject_reason' => $this->whenNotNull($this->reject_reason),
            'created_at' => $this->created_at,
            'views'  => $this->views,
            'updated_at' => $this->whenNotNull($this->updated_at),
            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'id' => $this->provider->id,
                    'first_name' => $this->provider->first_name,
                    'last_name' => $this->provider->last_name,
                    'photo' => $this->provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
                    'user'=> [
                        'id' => $this->provider->user->id,
                        'email'=> $this->provider->user->email,
                    ]
                ];
            })
        ];
    }
}
