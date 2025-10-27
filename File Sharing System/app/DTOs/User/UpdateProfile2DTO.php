<?php

namespace App\DTOs\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;

class UpdateProfile2DTO
{
    public function __construct(
        public readonly string       $name,
        public readonly UploadedFile $image
    )
    {
    }

    public static function fromRequest(array $user ,UploadedFile $image): ?UpdateProfile2DTO
    {
        if (!$user) {
            return null;
        }
        return new self(
            $user['name'],
            image: $image
        );
    }

    public static function fromRequest2(array $user): ?UpdateProfile2DTO
    {
        if (!$user) {
            return null;
        }
        return new self(
            $user['name'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'image_path' => $this->image
        ];
    }

    public function toArray2(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
