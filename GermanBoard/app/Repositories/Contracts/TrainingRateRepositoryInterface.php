<?php

namespace App\Repositories\Contracts;

use App\Models\TrainingRate;

interface TrainingRateRepositoryInterface
{
    public function create ($data):?TrainingRate;
    public function getByTrainingIdAndTraineeId(int $training_id, int $trainee_id): TrainingRate | null;
}
