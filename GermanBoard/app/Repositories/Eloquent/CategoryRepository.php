<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return Category::all();
    }

    public function getById(int $id): Category
    {
        return Category::select('id', 'name_ar', 'name_en', 'name_du')
            ->where('id', $id)
            ->first();
    }

    public function getIdsByName(array $data): array
    {
        $categories = collect($data)->map(function ($item) {
            return Category::firstOrNew(['name' => $item['name']], $item);
        });
        $categories->each->save();

        return $categories->toArray();
    }
}
