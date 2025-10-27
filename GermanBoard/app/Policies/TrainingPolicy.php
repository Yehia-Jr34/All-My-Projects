<?php

namespace App\Policies;

use App\Models\Training;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TrainingPolicy
{
   public function ownTraining(User $user,Training $training):bool {
       return $user->provider->id == $training->provider_id;
   }
}
