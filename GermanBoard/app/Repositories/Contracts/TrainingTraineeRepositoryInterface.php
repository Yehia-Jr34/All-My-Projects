<?php

namespace App\Repositories\Contracts;

use App\Models\TrainingTrainee;
use Illuminate\Support\Collection;

interface TrainingTraineeRepositoryInterface
{
    public function store(array $data): TrainingTrainee;

    public function getById(int $training_trainee_id): ?TrainingTrainee;

    public function update(int $training_trainee_id, array $data): void;

    public function getTraineeIdsByTrainingId(int $training_id): array;

    public function getTraineeByPaymentIntent(string $payment_intent): ?TrainingTrainee;

    public function checkIfUserEnrolledBefore(int $trainee_id, int $training_id): ?TrainingTrainee;

    public function getEnrolledTrainings(int $id): Collection;

    public function getVideosAndExams(int $training_id, int $trainee_id): TrainingTrainee|null;

    public function getLiveEnrolled(int $training_id): Collection;

    public function trainees(int $training_id): Collection;

    public function index(): Collection;

    public function unAssignedCertificates(): Collection;

    public function updateByTrainingIdAndTraineeId(int $training_id, int $trainee_id, array $data): bool;

    public function getByTrainingIdAndTraineeId(int $training_id, int $trainee_id): ?TrainingTrainee;

    public function getByCertificationCode(string $code): ?TrainingTrainee;

    public function ensure_enrolled(int $training_id, int $trainee_id): TrainingTrainee|null;
}
