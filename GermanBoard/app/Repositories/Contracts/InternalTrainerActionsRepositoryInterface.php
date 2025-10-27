<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface InternalTrainerActionsRepositoryInterface
{

    public function create(array $data);

    public function getAction($internal_trainer_id) : Collection;

}
