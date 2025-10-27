<?php

namespace App\Repositories\Eloquent;

use App\Enum\AppConstants;
use App\Models\GlobalArticle;
use App\Models\Provider;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProviderRepository implements ProviderRepositoryInterface
{
    public function getProviderByUserId(int $userId): ?Provider
    {
        return Provider::with(['user','memberships'])
            ->select('id', 'first_name', 'user_id','last_name', 'gender', 'phone_number', 'date_of_birth', 'brief', 'specialized_at', 'is_complete_profile')
            ->where('user_id', $userId)
            ->first();
    }

    public function getUserIdByPhoneNumber(string $phoneNumber): ?Provider
    {
        return Provider::select('user_id')
            ->where('phone_number', $phoneNumber)
            ->first();
    }

    public function getById(int $id): Provider
    {
        // TODO: Implement getById() method.
    }

    public function getInternalTrainers(Provider $provider): Collection
    {
        return $provider->internalTrainers()->with('user')->get();
    }

    public function create(array $data): Provider
    {
        return Provider::create($data);
    }

    public function getAll(): Collection
    {
        return Provider::with(['user', 'memberships'])
            ->whereDoesntHave('user', function($query) {
                $query->where('email', AppConstants::ADMIN_EMAIL->value);
            })
            ->get()
            ->sortByDesc('memberships.expired_at');
    }

    public function getFullData(int $id): Provider
    {
        return Provider::with(['user', 'memberships'])
            ->where('id', $id)
            ->first();
    }

    public function complete(array $data, int $id): Provider
    {
        Provider::where('user_id', $id)->update($data);

        return Provider::where('user_id', $id)->first();
    }

    public function trainings($provider_id): Provider|null
    {
        $provider = Provider::with([
            'trainings' => function ($query) {
            $query->orderBy('type');
        },
        ])->find($provider_id);

        return $provider;
    }

    public function internalTrainers($provider_id): Provider|null
    {
        $provider = Provider::with(['internalTrainers.user'])->find($provider_id);

        return $provider;
    }

    public function search(string $query): Collection|null
    {
        return Provider::with(['user'])->where('nickname', 'like', "%{$query}%")
            ->get();
    }

    public function getProviderForTrainee($id){
        return Provider::with(['trainings.training_trainees'])->find($id);
    }
}
