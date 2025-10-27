<?php

namespace App\Repositories\Contracts;

use App\Models\TraineeVideo;

interface TraineeVideoRepositoryInterface
{
    public function insert(int $trainee_id, int $video_id): TraineeVideo;

}
