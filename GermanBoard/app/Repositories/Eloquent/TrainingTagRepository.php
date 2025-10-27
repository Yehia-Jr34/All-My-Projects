<?php

namespace App\Repositories\Eloquent;

use App\Models\TrainingTag;
use App\Repositories\Contracts\TrainingTagRepositoryInterface;

class TrainingTagRepository implements TrainingTagRepositoryInterface
{
    public function store(array $data): void
    {
        TrainingTag::insert($data);
    }
}
