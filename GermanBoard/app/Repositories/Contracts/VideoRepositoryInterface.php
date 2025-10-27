<?php

namespace App\Repositories\Contracts;

use App\Models\Video;

interface VideoRepositoryInterface
{
    public function create(array $data): void;
    public function getWithRelations(int $id):Video;
    public function update(Video $video): void;
    public function getTitles(int $training_id): array;
}
