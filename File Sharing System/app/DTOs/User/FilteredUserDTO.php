<?php
declare(strict_types=1);

namespace App\DTOs\User;


use AllowDynamicProperties;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

#[AllowDynamicProperties] class FilteredUserDTO {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $username,
    ) {}

    public static function fromUser (User|Authenticatable|null $user) :?FilteredUserDTO{
        if(!$user){
            return null;
        }
        return new self(
            $user->id,
            $user->name,
            $user->username,
        );
    }

    public static function collectionFromModels($users): array
    {
        return $users->map(fn($user) => self::fromUser($user))->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username'=>$this->username,
        ];
    }

    public function toArrayWithImage(string $image_path , $code = ''): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username'=>$this->username,
            'image_path' => $image_path,
            'otp' => $code
        ];
    }
}
