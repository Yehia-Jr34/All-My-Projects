<?php

namespace App\Services\Agora;


use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use App\Repositories\Contracts\TrainingSessionsRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Yasser\Agora\RtcTokenBuilder;

class AgoraService
{
    public function __construct(
        private readonly TrainingSessionsRepositoryInterface $trainingSessionsRepository,
        private readonly TrainingTraineeRepositoryInterface $trainingTraineeRepository,

    ) {}

    public  function generateToken($trainingSessionId)
    {
        $trainingSession = $this->trainingSessionsRepository->getById($trainingSessionId);
        $user = auth()->user();

        if($user->hasRole(RolesEnum::TRAINEE->value)){
            $this->validateTraineeCanAccessTraining($trainingSession->training_id , $user->trainee->id);
        }

        elseif($user->hasRole(RolesEnum::INTERNAL_TRAINER->value)){
            $this->validateInternalTrainerCanAccessTraining($trainingSession->training_id);
        }

        else{
            $this->validateProviderCanAccessTraining($trainingSession->training_id);
        }

        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $user = auth()->user()->name;
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600 * 2 ;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        $rtcToken = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $trainingSessionId, $user, $role, $privilegeExpiredTs);
        return $rtcToken;
    }

    // Helper methods
    private function validateTraineeCanAccessTraining($training_id , $trainee_id ){
        if (!$this->trainingTraineeRepository->ensure_enrolled($training_id , $trainee_id )){
            throw new \DomainException('unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
        }
    }

    private function validateInternalTrainerCanAccessTraining($training_id){
      $training = auth()->user()->internalTrainer->trainings->where('id', $training_id)->first();

      if(!$training){
          throw new \DomainException('unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
      }

    }

    private function validateProviderCanAccessTraining($training_id){
        $training = auth()->user()->provider->trainings()->where('id', $training_id)->first();
        if(!$training){
            throw new \DomainException('unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
        }

    }
}
