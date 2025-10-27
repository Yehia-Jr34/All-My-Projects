<?php

namespace App\Repositories\Contracts;

use App\Models\TrainingSession;
use Illuminate\Support\Collection;

interface TrainingSessionsRepositoryInterface
{
    public function add(Collection $data): void;

    public function getByTrainingId(int $training_id): Collection;

    public function getById(int $id): TrainingSession;

    public function update(TrainingSession $trainingSession): void;
}
