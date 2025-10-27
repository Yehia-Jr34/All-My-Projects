<?php

namespace App\Services\Provider;


use App\Enum\ProvidersMediaEnum;
use App\Enum\StatusCodeEnum;
use App\Models\Provider;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProviderService
{
    public function __construct(
        private UserRepositoryInterface            $userRepository,
        private ProviderRepositoryInterface        $providerRepository,
        private MembershipRepositoryInterface      $membershipRepository,
        private TrainingTraineeRepositoryInterface $trainingTraineeRepository,
    ) {}

    public function create(array $data): array
    {
        return DB::transaction(function () use($data){
            $user_data = [
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'email_verified_at' => Carbon::now(),
            ];

            $user = $this->userRepository->createUser($user_data);

            $provider_data = [
                'user_id' => $user->id,
                'balance'=> 0
            ];

            $provider = $this->providerRepository->create($provider_data);

            $user->assignRole($data['role']);

            $membership_data = [
                'provider_id' => $provider->id,
                'start_at' => $data['membership_start_date'],
                'expired_at' => $data['membership_expiry_date'],
                'remind_at' => Carbon::parse($data['membership_expiry_date'])->subMonth(),
            ];

            $membership = $this->membershipRepository->create($membership_data);
            return [
                'user_id' => $user->id,
                'email' => $data['email'],
                'password' => $data['password'],
                'provider_id' => $provider->id,
                'membership_id' => $membership->id,
                'start_at' => now()->format('Y-m-d'),
                'expire_at' => $data['membership_expiry_date'],
                'remind_at' => Carbon::parse($data['membership_expiry_date'])->subMonth()->format('Y-m-d'),
            ];
        });

    }

    public function index(): Collection
    {
        return $this->providerRepository->getAll()->map(function ($item){
            return array_merge($item->toArray(),[
                'role' => $item->user->roles()->first()->name
            ]);
        });
    }

    public function show(int $provider_id): Provider
    {
        $provider = $this->providerRepository->getFullData($provider_id);
        $provider['photo']= $provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value);
        $provider['cover']= $provider->getFirstMediaUrl(ProvidersMediaEnum::COVER->value);
        $provider['role'] =  $provider->user->roles()->first()->name;
        unset($provider['media']);

        return $provider;
    }

    public function complete(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = request()->user();

            $user->update([
                'password' => bcrypt($data['password']),
            ]);

            unset($data['password']);

            $profileData = Arr::except($data, ['photo', 'cover']);

            $provider = $this->providerRepository->complete(
                array_merge($profileData, ["is_complete_profile" => true]),
                $user->id
            );

            if (isset($data['photo'])) {
                $provider->addMedia($data['photo'])
                    ->toMediaCollection(ProvidersMediaEnum::PHOTO->value);
            }

            if (isset($data['cover'])) {
                $provider->addMedia($data['cover'])
                    ->toMediaCollection(ProvidersMediaEnum::COVER->value);
            }
            $provider['photo']= $provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value);
            return $provider;
        });
    }

    public function trainings($provider_id): Provider
    {

        $provider = $this->providerRepository->trainings($provider_id);

        if (!$provider) {
            throw new \DomainException('Not Found', StatusCodeEnum::NOT_FOUND->value);
        }

        return $provider;
    }

    public function trainees(int $training_id): array
    {
        $trainees = $this->trainingTraineeRepository->trainees($training_id);

        $data = collect($trainees)->map(function ($trainee) {
            return [
                'id' => $trainee->trainee->id,
                'first_name' => $trainee->trainee->first_name,
                'last_name' => $trainee->trainee->last_name,
                'gender' => $trainee->trainee->gender,
                'phone_number' => $trainee->trainee->phone_number,
                'date_of_birth' => $trainee->trainee->date_of_birth,
                'country' => $trainee->trainee->country,
                'address' => $trainee->trainee->address,
                'user_id' => $trainee->trainee->user_id,
                'email' => $trainee->trainee->user->email,
            ];
        });

        return $data->toArray();
    }

    public function internalTrainers($provider_id): Provider
    {
        $provider = $this->providerRepository->internalTrainers($provider_id);

        if (!$provider) {
            throw new \DomainException('Not Found', StatusCodeEnum::NOT_FOUND->value);
        }

        return $provider;
    }

    public function getProviderForTrainee($provider_id): Provider
    {
        $provider = $this->providerRepository->getProviderForTrainee($provider_id);

        if (!$provider) {
            throw new \DomainException('Not Found', StatusCodeEnum::NOT_FOUND->value);
        }

        return $provider;
    }
}
