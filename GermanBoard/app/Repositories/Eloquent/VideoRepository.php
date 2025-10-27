<?php

namespace App\Repositories\Eloquent;

use App\Models\Video;
use App\Repositories\Contracts\VideoRepositoryInterface;

class VideoRepository implements VideoRepositoryInterface
{
    public function create(array $data): void
    {
        Video::create($data);
    }

    public function getWithRelations(int $id): Video
    {
        return Video::with([
            'training',
        ])->find($id);
    }

    public function update(Video $video): void
    {
        $video->save();
    }

    public function getTitles(int $training_id): array
    {
        return Video::select('title', 'id')->where('training_id', $training_id)->get()->toArray();
    }
}
