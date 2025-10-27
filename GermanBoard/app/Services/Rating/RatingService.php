<?php

namespace App\Services\Rating;

use App\Enum\NotificationTypesEnum;
use App\Jobs\RecalculateRatings;
use App\Jobs\SendNotification;
use App\Repositories\Contracts\TrainingRateRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;

class RatingService
{
    public function __construct(
        private readonly TrainingRateRepositoryInterface $trainingRateRepository,
        private readonly TrainingTraineeRepositoryInterface $trainingTraineeRepository
    ) {}

    public function store(array $data)
    {
        $user = auth()->user();

        if (!$user->hasRole('trainee')) {
            throw new \DomainException('You are not allowed to add a rating', 401);
        }

        $trainingTrainee = $this->trainingTraineeRepository->checkIfUserEnrolledBefore($user->trainee->id, $data['training_id']);

        if (!$trainingTrainee) {
            throw new \DomainException('You are not allowed to add a rating', 401);
        }

        $rating = $this->trainingRateRepository->getByTrainingIdAndTraineeId($data['training_id'], $user->trainee->id);


        if ($rating) {
            $rating->update([
                'value' => $data['value'],
            ]);
            RecalculateRatings::dispatch($data['training_id']);
            return $rating;
        }

        $rating = $this->trainingRateRepository->create([
            'trainee_id' => $user->trainee->id,
            'training_id' => $data['training_id'],
            'value' => $data['value'],
        ]);

        RecalculateRatings::dispatch($data['training_id']);

        return $rating;
    }
}
