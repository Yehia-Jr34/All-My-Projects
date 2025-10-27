<?php

namespace App\Repositories\Contracts;

use App\DTOs\Group\GroupDTO;
use App\DTOs\Group\GroupInfoDTO;
use App\DTOs\User\UserDTO;
use App\Models\Group;
use App\Models\UserGroup;

interface GroupRepositoryInterface
{

    public function create(string $name, string $description, int $created_by): GroupDTO;

    public function getGroupById(int $group_id): ?Group;

    public function getGroupDetails(int $group_id, UserDTO $userDTO): GroupInfoDTO;

    public function getGroupWithMembers(int $group_id): ?Group;

    public function getGroupWithFiles(int $group_id): ?Group;

    public function find(int $group_id): ?Group;

    public function update(Group $group, array $array): void;

    public function delete(UserGroup $oldest_group_member): void;

    public function editGroupName(int $group_id, string $name): Group;

    public function removeUsersFromGroup($groupId, array $userIds): void;


}
