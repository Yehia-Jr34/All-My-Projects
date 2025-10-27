<?php

namespace App\Repositories\Contracts;

use App\Models\InternalTrainer;
use Illuminate\Support\Collection;

interface InternalTrainerRepositoryInterface
{
    public function create(array $data, int $provider_id): InternalTrainer;

    public function getById(int $internal_trainer_id): InternalTrainer|null;

    public function attach(int $internal_trainer_id, int $training_id);

    public function getWithTrainings(int $internal_trainer_id): InternalTrainer;

    public function isAdminInternalTrainer(int $internal_trainer_id): InternalTrainer|null;

    public function getByUserId(int $user_id): InternalTrainer|null;

}
