<?php

namespace App\Http\Resources\Blog;

use App\Enum\BlogMediaCollectionEnum;
use App\Enum\ProvidersMediaEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetMyArticleResource extends JsonResource
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
            'views'  => $this->views,
            'created_at' => $this->created_at,
            'categories' => $this->global_article_categories->map(function ($item) {
                return [
                    'id' =>$item->category->id,
                    'name_ar' => $item->category->name_ar,
                    'name_en' => $item->category->name_en,
                    'name_du' => $item->category->name_du,
                ];
            }),
            'updated_at' => $this->whenNotNull($this->updated_at),
        ];
    }
}
