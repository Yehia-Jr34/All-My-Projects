<?php

namespace App\Repositories\Contracts;

use App\DTOs\Image\ImageDTO;

interface UserImageRepositoryInterface
{
    public function store (int $user_id, string $image_path): string;

    public function getUserImage(int $user_id): ImageDTO;

    public function update(int $user_id, string $image_path): string;
}
