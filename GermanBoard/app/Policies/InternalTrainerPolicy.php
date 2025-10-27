<?php

namespace App\Policies;

use App\Models\InternalTrainer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InternalTrainerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function haveInternalTrainer(User $user , InternalTrainer $internalTrainer): bool
    {
        $items = $user->provider->internalTrainers->filter(function($item) use($internalTrainer){
            return $item->id == $internalTrainer->id;
        });

        return (bool)sizeof($items);

    }


}
