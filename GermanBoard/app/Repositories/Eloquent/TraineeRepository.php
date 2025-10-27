<?php

namespace App\Repositories\Eloquent;

use App\Models\Trainee;
use App\Repositories\Contracts\TraineeRepositoryInterface;
use Illuminate\Support\Collection;

class TraineeRepository implements TraineeRepositoryInterface
{
    public function register(array $data): ?Trainee
    {
        return Trainee::create($data);
    }

    public function getTraineeData(int $user_id): ?Trainee
    {
        return Trainee::select('first_name', 'last_name', 'country', 'address', 'phone_number', 'gender', 'id', 'date_of_birth')
            ->where('user_id', $user_id)
            ->first();
    }

    public function getUserByPhoneNumber(string $phone_number): ?Trainee
    {
        return Trainee::select('user_id')
            ->where('phone_number', $phone_number)
            ->first();
    }

    public function getMyTrainings($id)
    {
        return Trainee::with(['trainings' => function ($query) {
            $query->wherePivot('payment_status', 'success')->with('provider');
        }])
            ->find($id);
    }

    public function index(): Collection
    {
        return Trainee::with(['user'])->get();
    }

    public function show($trainee_id): Trainee | null
    {
        return Trainee::with(['user', 'trainings.provider.user','trainings.training_trainees.certificate'])->find($trainee_id);
    }
}
