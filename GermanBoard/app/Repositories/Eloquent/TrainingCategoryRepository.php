<?php

namespace App\Repositories\Eloquent;

use App\Models\TrainingCategory;
use App\Repositories\Contracts\TrainingCategoryRepositoryInterface;

class TrainingCategoryRepository implements TrainingCategoryRepositoryInterface
{
    public function store(array $data): void
    {
        TrainingCategory::insert($data);
    }
}
