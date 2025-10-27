<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface UserReportRepositoryInterface
{
    public function get(int $group_id): Collection;

    public function store(array $data): void;
}
