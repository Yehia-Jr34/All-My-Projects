<?php

namespace App\Repositories\Eloquent;

use App\Models\TrainingRate;
use App\Repositories\Contracts\TrainingRateRepositoryInterface;

class TrainingRateRepository implements TrainingRateRepositoryInterface
{


    public function getByTrainingIdAndTraineeId(int $training_id, int $trainee_id): TrainingRate | null
    {
        return TrainingRate::where('training_id', $training_id)
            ->where('trainee_id', $trainee_id)
            ->first();
    }

    public function create($data): ?TrainingRate
    {
        return TrainingRate::create($data);
    }
}
