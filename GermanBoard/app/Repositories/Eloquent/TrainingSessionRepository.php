<?php

namespace App\Repositories\Eloquent;

use App\Models\TrainingSession;
use App\Repositories\Contracts\TrainingSessionsRepositoryInterface;
use Illuminate\Support\Collection;

class TrainingSessionRepository implements TrainingSessionsRepositoryInterface
{
    public function add(Collection $data): void
    {
        TrainingSession::insert($data->toArray());
    }

    public function getByTrainingId(int $training_id): Collection
    {
        return TrainingSession::select('id', 'date_time', 'notes', 'status', 'title')
            ->where('training_id', $training_id)
            ->get();
    }

    public function getById(int $id): TrainingSession
    {
        return TrainingSession::find($id);
    }

    public function update(TrainingSession $trainingSession): void
    {
        $trainingSession->update($trainingSession->toArray());
    }
}
