<?php
declare(strict_types=1);

namespace App\DTOs\User;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

class UserDTO
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $email,
        public readonly string  $username,
        public readonly ?Carbon $email_verified_at,
        public readonly ?string  $image_path
    )
    {
    }

    public static function fromUser(User|Authenticatable|null $user, string $image_path): ?UserDTO
    {
        if (!$user) {
            return null;
        }
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->username,
            $user->email_verified_at,
            $image_path
        );
    }

    public static function fromUserRepo(User|Authenticatable|null $user): ?UserDTO
    {
        if (!$user) {
            return null;
        }
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->username,
            $user->email_verified_at,
            null
        );
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'email_verified_at' => $this->email_verified_at,
            'image_path' => $this->image_path
        ];
    }
}
