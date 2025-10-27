<?php

namespace App\Repositories\Contracts;

use App\Models\Trainee;
use Illuminate\Support\Collection;

interface TraineeRepositoryInterface
{
    public function register(array $data): ?Trainee;

    public function getTraineeData(int $userId): ?Trainee;

    public function getUserByPhoneNumber(string $phone_number): ?Trainee;

    public function getMyTrainings($id);

    public function index(): Collection;

    public function show($trainee_id): Trainee | null;
}
