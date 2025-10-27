<?php

namespace App\DTOs\Group;

use App\DTOs\User\UserDTO;

class GroupInfoDTO
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $description,
        public readonly UserDTO $owner,
    )
    {
    }

    public static function create(string $name, string $description, UserDTO $owner): self
    {
        return new self (
            name: $name,
            description: $description,
            owner: $owner
        );
    }
}
