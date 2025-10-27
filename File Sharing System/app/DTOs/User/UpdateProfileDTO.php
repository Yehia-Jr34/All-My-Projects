<?php

namespace App\DTOs\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;

class UpdateProfileDTO
{
    public function __construct(
        public readonly string       $name,
        public readonly string       $email,
        public readonly string       $username,
        public readonly UploadedFile $image
    )
    {
    }

    public static function fromRequest(array $user ,UploadedFile $image): ?UpdateProfileDTO
    {
        if (!$user) {
            return null;
        }
        return new self(
            $user['name'],
            $user['email'],
            $user['username'],
            image: $image
        );
    }

    public static function fromRequest2(array $user): ?UpdateProfileDTO
    {
        if (!$user) {
            return null;
        }
        return new self(
            $user['name'],
            $user['email'],
            $user['username'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'image_path' => $this->image
        ];
    }
}
