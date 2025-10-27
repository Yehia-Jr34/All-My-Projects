<?php

namespace App\Repositories\Contracts;

use App\Models\Training;
use Illuminate\Support\Collection;

interface TrainingRepositoryInterface
{
    public function create(array $data): Training;

    public function getById(int $id): Training | null;

    public function getDetails(int $id): Training;

    public function getRecordedTraining(int $id): Training;

    public function getTraining(int $id): Training|null;

    public function getOwnerId(int $id): Training;

    public function getDataForHomePage(): Collection;

    public function getTrainings(int $provider_id, string $type): Collection;

    public function getOngoingTrainings(int $provider_id, string $type = 'live'): Collection;

    public function getCompletedTrainings(int $provider_id, string $type = 'live'): Collection;

    public function getNotStartedTrainings(int $provider_id, string $type = 'live'): Collection;

    public function getTrainingTitles(int $provider_id, string $type): Collection;

    public function getTrainingWithTraineesById(int $training_id): Training;

    public function isAdminTraining(int $training_id): Training | null;

    public function byCategory(int $category_id): Collection;

    public function search(string $query): Collection|null;

    public function getTrainingsByIds(array $training_ids, string $type): Collection;

    public function getOngoingTrainingsByIds(array $training_ids, string $type = 'live'): Collection;

    public function getCompletedTrainingsByIds(array $training_ids, string $type = 'live'): Collection;

    public function getNotStartedTrainingsByIds(array $training_ids, string $type = 'live'): Collection;

    public function getByProvider($provider_id): Collection;

    public function getAll(): Collection;

    public function update(int $id, array $data): bool;
}
