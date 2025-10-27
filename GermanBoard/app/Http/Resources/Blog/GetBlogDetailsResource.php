<?php

namespace App\Http\Resources\Blog;

use App\Enum\BlogMediaCollectionEnum;
use App\Enum\ProvidersMediaEnum;
use App\Http\Resources\Provider\ProviderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class GetBlogDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $categoryNameField = 'name_'.$locale;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'categories' => $this->global_article_categories->map(function ($item) use ($categoryNameField) {
                return [
                    'id' =>$item->category->id,
                    'name' => $item->category->$categoryNameField,
                ];
            }),
            'views' => $this->views,
            'date' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'imageUrl' => $this->getFirstMediaUrl(BlogMediaCollectionEnum::Image->value),
            'provider' => ProviderResource::make($this->provider)
        ];
    }
}
