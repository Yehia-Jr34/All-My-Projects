<?php

declare(strict_types=1);

namespace App\Services\Category;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function get(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    public function create(array $validated)
    {
        Category::create($validated);
    }

    public function update(array $validated)
    {
        Category::find($validated['category_id'])->update($validated);
    }
}
