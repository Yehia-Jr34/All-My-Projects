<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function ownsGroup(User $user, Group $group): bool
    {
        return $group->created_by === $user->id;
    }

    public function isAdmin(User $user, Group $group): bool
    {

        return $group->users()
            ->where('user_id', $user->id)
            ->where('role', RolesEnum::ADMIN->value)
            ->exists();
    }

    public function isMember(User $user, Group $group): bool
    {

        return $group->users()
            ->where('user_id', $user->id)
            ->where('role', RolesEnum::MEMBER->value)
            ->exists();
    }
}
