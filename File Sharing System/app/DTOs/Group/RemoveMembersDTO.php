<?php

namespace App\DTOs\Group;

class RemoveMembersDTO
{
    public function __construct(
        public readonly string $group_id,
        public readonly array $members,

    ) {}

    public static function fromArray (array $data ) : ?self {

        return new self(
            $data['group_id'],
            $data['members'] ?? []
        );

    }

    public function toArray(): array
    {
        return [

        ];
    }
}
