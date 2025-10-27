<?php

namespace App\Repositories\Eloquent;

use App\Models\GlobalArticleCategory;
use App\Repositories\Contracts\GlobalArticleCategoryRepositoryInterface;

class GlobalArticleCategoryRepository implements GlobalArticleCategoryRepositoryInterface
{
    public function add(array $data): void
    {
        GlobalArticleCategory::insert($data);
    }
}
