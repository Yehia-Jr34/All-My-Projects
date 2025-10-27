<?php

namespace App\DTOs\Group;

class EditGroupNameDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int    $group_id,

    )
    {
    }

    public static function fromArray(array $data): ?self
    {
        return new self(
            $data['name'],
            $data['group_id'],
        );

    }

    public function toArray(): array
    {
        return [

        ];
    }
}
