<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface TagRepositoryInterface
{
    public function storeOrFind(array $data): array;

    public function get(string $searchQuery): Collection;
}
