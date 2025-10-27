<?php

namespace App\Repositories\Eloquent;

use App\Models\UserGroup;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use Illuminate\Support\Collection;

class UserGroupRepository implements UserGroupRepositoryInterface
{

    public function create(array $data): void
    {
        UserGroup::create($data);
    }

    public function findUserGroup(int $group_id, int $user_id): ?UserGroup
    {
        return UserGroup::where('group_id', $group_id)->where('user_id', $user_id)->get()->first();
    }

    public function findOldestMember(int $group_id): ?UserGroup
    {
        return UserGroup::where('group_id', $group_id)->get()->first();
    }

    public function delete(UserGroup $group_member): void
    {
        $group_member->delete();
    }

    public function deleteMembers(int $group_id, array $ids): void
    {
        UserGroup::where('group_id', $group_id)
            ->whereIn('user_id', $ids)
            ->delete();
    }

    public function getMembersIds(int $group_id): Collection
    {
        return UserGroup::select(['id', 'user_id', 'group_id'])
            ->where('group_id', $group_id)
            ->get();
    }
}
