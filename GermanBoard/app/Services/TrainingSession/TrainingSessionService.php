<?php

namespace App\Services\TrainingSession;

use App\Enum\StatusCodeEnum;
use App\Repositories\Contracts\TrainingSessionsRepositoryInterface;
use App\Services\Agora\AgoraService;

class TrainingSessionService
{
    public function __construct(
        private TrainingSessionsRepositoryInterface $trainingSessionsRepository,
        private AgoraService $agoraService,

    ) {}

    public function createSession(array $data): void
    {
        $user = auth()->user();
        if ($user->hasRole('trainee') || $user->hasRole('admin')) {
            throw new \DomainException('unauthorized', 403);
        }

        $training_session = $this->trainingSessionsRepository->getById($data['training_session_id']);

        if ($user->id !== $training_session->training->provider_id) {
            throw new \DomainException('you can only create sessions in your trainings', StatusCodeEnum::BAD_REQUEST->value);
        }

        $training_session->status = 'ongoing';
        $this->trainingSessionsRepository->update($training_session);
    }


}
