<?php

namespace App\DTOs\Group;

class GroupDTO
{
    public function __construct(
        public int $id,
        public string $group_name,
        public string $group_description,
        public ?string $role,
        public string $owner_name,
        public string $owner_username
    ) {}

    public static function fromModel($group): self
    {
        return new self(
            id: $group->id,
            group_name: $group->name,
            group_description: $group->description,
            role: $group?->pivot?->role, // Extract role from the pivot table
            owner_name: $group->owner->name ?? '',
            owner_username: $group->owner->username ?? ''
        );
    }

    public static function collection($groups): array
    {
        return $groups->map(fn ($group) => self::fromModel($group))->toArray();
    }
}
