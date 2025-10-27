<?php

declare(strict_types=1);

namespace App\Services\InternalTrainer;

use App\Enum\InternalTrainerMediaCollection;
use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use App\Models\InternalTrainer;
use App\Repositories\Contracts\InternalTrainerActionsRepositoryInterface;
use App\Repositories\Contracts\InternalTrainerRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InternalTrainerService
{
    public function __construct(
        private readonly InternalTrainerRepositoryInterface $internalTrainerRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly ProviderRepositoryInterface $providerRepository,
        private readonly TrainingRepositoryInterface $trainingRepository,
        private readonly InternalTrainerActionsRepositoryInterface $internalTrainerActionsRepository,

    ) {}

    public function create(array $data)
    {
        DB::transaction(function () use ($data) {
            $provider_id = auth()->user()->provider->id;

            $user = $this->userRepository->createUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'email_verified_at'=> now()
            ]);

            $user->assignRole(RolesEnum::INTERNAL_TRAINER->value);

            $data['user_id'] = $user->id;

            $internalTrainer = $this->internalTrainerRepository->create($data, $provider_id);

            if (isset($data['profile_image'])) {

                $internalTrainer->addMedia($data['profile_image'])->toMediaCollection(InternalTrainerMediaCollection::PROFILE_IMAGE->value);
            }
        });
    }

    public function indexByProvider(): Collection
    {

        $provider = auth()->user()->provider;

        return $this->providerRepository->getInternalTrainers($provider);
    }

    public function assignTraining(array $data)
    {
        $user = auth()->user();

        // can manage the internal trainer
        $internal_trainer = $this->internalTrainerRepository->getById($data['internal_trainer_id']);

        if (!$internal_trainer) {
            throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
        }

        if ($user->cannot('haveInternalTrainer', $internal_trainer)) {
            throw new \DomainException("You are not authorized", StatusCodeEnum::UNAUTHORIZED->value);
        }

        // can manage the training
        $training = $this->trainingRepository->getById($data['training_id']);

        if (!$training) {
            throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
        }

        if ($user->cannot('ownTraining', $training)) {
            throw new \DomainException("You are not authorized", StatusCodeEnum::UNAUTHORIZED->value);
        }


        $is_have_training = $internal_trainer->trainings->where('id',$training->id)->first();

        if($is_have_training){
            throw new \DomainException("This Training assigned to this trainer before", StatusCodeEnum::BAD_REQUEST->value);
        }

        // assign internal trainer to training
        $this->internalTrainerRepository->attach($internal_trainer->id, $training->id);
    }

    public function getInternalTrainerTrainings(int $internal_trainer_id): InternalTrainer
    {
        $user = auth()->user();

        // can manage the internal trainer
        $internal_trainer = $this->internalTrainerRepository->getById($internal_trainer_id);

        if (!$internal_trainer) {
            throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
        }

        if ($user->cannot('haveInternalTrainer', $internal_trainer)) {
            throw new \DomainException("You are not authorized", StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $this->internalTrainerRepository->getWithTrainings($internal_trainer_id);
    }

    public function isAdminInternalTrainer(int $internal_trainer_id): bool
    {
        $admin = auth()->user();

        // Add null checks
        if (!$admin || !$admin->provider) {
            return false;
        }

        $admin_provider_id = $admin->provider->id;
        $internal_trainer = $this->internalTrainerRepository->isAdminInternalTrainer($internal_trainer_id);

        // Add null check for internal_trainer and its providers
        if (!$internal_trainer || !$internal_trainer->providers) {
            return false;
        }

        $provider = $internal_trainer->providers->first(function ($provider) use ($admin_provider_id) {
            return $provider->id === $admin_provider_id;
        });

        return (bool)$provider;
    }

    public function getActions($internal_trainer_id):Collection{
        return $this->internalTrainerActionsRepository->getAction($internal_trainer_id);
    }

}
