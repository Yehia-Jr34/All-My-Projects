<?php

namespace App\Repositories\Eloquent;

use App\Enum\VideoStatusEnum;
use App\Models\TraineeVideo;
use App\Repositories\Contracts\TraineeVideoRepositoryInterface;

class TraineeVideoRepository implements TraineeVideoRepositoryInterface
{

    public function insert(int $trainee_id, int $video_id): TraineeVideo
    {
        return TraineeVideo::create([
            'trainee_id' => $trainee_id,
            'video_id' => $video_id,
            'status' => VideoStatusEnum::COMPLETED->value
        ]);
    }
}
