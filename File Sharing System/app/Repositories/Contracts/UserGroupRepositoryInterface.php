<?php

namespace App\Repositories\Contracts;

use App\Models\UserGroup;
use Illuminate\Support\Collection;

interface UserGroupRepositoryInterface {

    public function create(array $data):void;

    public function findUserGroup(int $group_id, int $user_id) : ?UserGroup;

    public function findOldestMember(int $group_id) : ?UserGroup;

    public function delete(UserGroup $group_member):void;

    public function deleteMembers (int $group_id, array $ids): void;

    public function getMembersIds(int $group_id): Collection;

}
