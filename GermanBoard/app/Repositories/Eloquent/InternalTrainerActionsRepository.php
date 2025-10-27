<?php

namespace App\Repositories\Eloquent;

use App\Models\InternalTrainerAction;
use App\Repositories\Contracts\InternalTrainerActionsRepositoryInterface;
use Illuminate\Support\Collection;

class InternalTrainerActionsRepository implements InternalTrainerActionsRepositoryInterface
{

    public function create(array $data)
    {
        InternalTrainerAction::create($data);
    }

    public function getAction($internal_trainer_id):Collection
    {
        return InternalTrainerAction::with('training')->where('internal_trainer_id',$internal_trainer_id)->get();
    }
}
