<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Support\Collection;

class TagRepository implements TagRepositoryInterface
{
    public function storeOrFind(array $data): array
    {
        $categories = collect($data)->map(function ($item) {
            return Tag::firstOrNew(['name' => $item['name']], $item);
        });
        $categories->each->save();

        return $categories->toArray();
    }

    public function get(string $searchQuery): Collection
    {
        return Tag::where('name', 'LIKE', "%{$searchQuery}%")->limit(10)->get();
    }
}
