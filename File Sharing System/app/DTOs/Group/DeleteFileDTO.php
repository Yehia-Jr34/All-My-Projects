<?php

namespace App\DTOs\Group;

class DeleteFileDTO
{
    public function __construct(
        public readonly string $group_id,
        public readonly array  $files,

    )
    {
    }

    public static function fromArray(array $data): ?self
    {

        return new self(
            $data['group_id'],
            $data['files'] ?? []
        );

    }

    public function toArray(): array
    {
        return [

        ];
    }
}
