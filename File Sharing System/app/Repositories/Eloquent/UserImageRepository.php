<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Image\ImageDTO;
use App\Models\Image;
use App\Repositories\Contracts\UserImageRepositoryInterface;

class UserImageRepository implements UserImageRepositoryInterface
{

    public function store(int $user_id, string $image_path): string
    {
        $image = Image::create([
            'user_id' => $user_id,
            'path' => $image_path
        ]);
        return $image->path;
    }

    public function getUserImage(int $user_id): ImageDTO
    {
        $image = Image::where('user_id', $user_id)->orderByDesc('created_at')->first();
        return ImageDTO::create($image?->path);
    }

    public function update(int $user_id, string $image_path): string
    {
        $image = Image::create([
            'user_id' => $user_id,
            'path' => $image_path
        ]);
        return $image->path;
    }
}
