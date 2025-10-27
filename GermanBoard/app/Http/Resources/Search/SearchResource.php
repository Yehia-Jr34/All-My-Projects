<?php

namespace App\Http\Resources\Search;

use App\Enum\BlogMediaCollectionEnum;
use App\Enum\ProvidersMediaEnum;
use App\Enum\TrainingMediaCollectionsEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $titleField = 'title_'.$locale;
        return [
            'providers' => $this['providers']->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->nickname,
                    'role' => $provider->user->roles()->first()->name ?? null,
                    'image' => $provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
                ];
            }),
            'trainings' => $this['trainings']->map(function ($training) use($titleField) {
                return [
                    'id' => $training->id,
                    'name' => $training->$titleField,
                    'cover' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value), // Adjust media collection name as needed
                    'learners' => $training->training_trainees()?->count(),
                    'instructor' => $training->provider->nickname,
                    'role' => $training->provider->user->roles()->first()->name ?? null,
                    'instructorImage' => $training->provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value) ?? null,
                    'type' => $training->type // You might want to make this dynamic based on your data
                ];
            }),
            'articles' => $this['articles']->map(function ($article) {
                $cleanContent = strip_tags($article->content);
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'author' => $article->provider->nickname ?? null,
                    'image' => $article->getFirstMediaUrl(BlogMediaCollectionEnum::Image->value), // Adjust media collection name as needed
                    'content' => strlen($cleanContent) > 100
                        ? substr($cleanContent, 0, 100)
                        : $cleanContent
                ];
            }),
        ];
    }
}
