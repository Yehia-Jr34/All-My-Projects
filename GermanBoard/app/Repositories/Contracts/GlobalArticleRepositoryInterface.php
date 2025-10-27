<?php

namespace App\Repositories\Contracts;

use App\Models\GlobalArticle;
use Illuminate\Support\Collection;

interface GlobalArticleRepositoryInterface
{
    public function getAll();

    public function getById(int $id);

    public function search(string $query): Collection|null;

    public function addView(int $id): void;

    public function create(array $data): GlobalArticle;

    public function update(int $id, array $data): GlobalArticle;

    public function delete(int $id): void;

    public function getAllWithProvider($status);

    public function getArticlesByProvider($provider_id ,$status);


}
