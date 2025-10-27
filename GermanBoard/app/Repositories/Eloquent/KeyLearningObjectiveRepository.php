<?php

namespace App\Repositories\Eloquent;

use App\Models\KeyLearningObjective;
use App\Repositories\Contracts\KeyLearningObjectiveRepositoryInterface;

class KeyLearningObjectiveRepository implements KeyLearningObjectiveRepositoryInterface
{
    public function store(array $data): void
    {
        KeyLearningObjective::insert($data);
    }
}
