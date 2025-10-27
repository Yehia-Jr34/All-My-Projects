<?php

namespace App\Repositories\Eloquent;


use App\Models\InternalTrainer;
use App\Repositories\Contracts\InternalTrainerRepositoryInterface;
use Illuminate\Support\Collection;

class InternalTrainerRepository implements InternalTrainerRepositoryInterface
{

    public function create(array $data, int $provider_id): InternalTrainer
    {
        $internalTriner = InternalTrainer::create($data);
        $internalTriner->providers()->attach($provider_id);
        return $internalTriner;
    }

    public function getById(int $internal_trainer_id): InternalTrainer|null
    {
        return InternalTrainer::find($internal_trainer_id);
    }

    public function attach(int $internal_trainer, int $training_id)
    {
        InternalTrainer::find($internal_trainer)->trainings()->attach($training_id);
    }

    public function getWithTrainings(int $internal_trainer_id): InternalTrainer
    {
        return InternalTrainer::with('trainings')->find($internal_trainer_id);
    }

    public function isAdminInternalTrainer(int $internal_trainer_id): InternalTrainer|null
    {
        return InternalTrainer::with('providers')->find($internal_trainer_id);
    }

    public function getByUserId(int $user_id): InternalTrainer|null
    {
        return InternalTrainer::where('user_id',$user_id)->first();
    }
}
