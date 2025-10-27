<?php

namespace App\Repositories\Contracts;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ProviderRepositoryInterface
{
    public function getById(int $id): Provider;

    public function getAll(): Collection;

    public function getProviderByUserId(int $userId): ?Provider;

    public function getUserIdByPhoneNumber(string $phoneNumber): ?Provider;

    public function getInternalTrainers(Provider $provider): Collection;

    public function create(array $data): Provider;

    public function getFullData(int $id): Provider;

    public function complete(array $data, int $id): Provider;

    public function trainings($provider_id): Provider|null;

    public function internalTrainers($provider_id): Provider|null;

    public function search(string $query): Collection|null;

    public function getProviderForTrainee($id);

}
