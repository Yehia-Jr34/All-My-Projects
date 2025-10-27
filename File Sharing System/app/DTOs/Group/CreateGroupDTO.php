<?php
declare(strict_types=1);

namespace App\DTOs\Group;



class CreateGroupDTO {
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $members,

    ) {}

    public static function fromArray (array $data ) : ?CreateGroupDTO {

        return new self(
            $data['name'],
            $data['description'],
            $data['members'] ?? []
        );

    }

    public function toArray(): array
    {
        return [

        ];
    }
}
