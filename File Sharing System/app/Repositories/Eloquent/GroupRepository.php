<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Group\GroupDTO;
use App\DTOs\Group\GroupInfoDTO;
use App\DTOs\User\UserDTO;
use App\Enums\FilesVersionsStatusEnum;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\UserImageRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository implements GroupRepositoryInterface
{

    public function __construct(
        protected readonly UserRepositoryInterface $userRepository,
        protected readonly UserImageRepositoryInterface $userImageRepository,
    )
    {
    }

    public function create(string $name, string $description, int $created_by): GroupDTO
    {
        return GroupDTO::fromModel(Group::create([
            "name" => $name,
            "description" => $description,
            "created_by" => $created_by
        ]));
    }

    public function getGroupById(int $group_id): ?Group
    {
        return Group::find($group_id);
    }

    public function getGroupWithMembers(int $group_id): ?Group
    {
        return Group::with('users')->find($group_id);

    }

    public function getGroupDetails(int $group_id, UserDTO $userDTO): GroupInfoDTO
    {
        $group = Group::findOrFail($group_id);

        return GroupInfoDTO::create($group->name, $group->description, $userDTO);

    }

    public function getGroupWithFiles(int $group_id): ?Group
    {
        return Group::with([
            'owner:id,name,username,email',
            'files' => function ($q) {
                $q->select(['id', 'name', 'status', 'group_id'])->where('admin_group_approve', 'accepted');
            },
            'files.versions' => function ($q) {
                $q->select(['id', 'user_id', 'created_at', 'file_id'])->where('path', '!=', null)->orderBy('id', 'desc');
            },
        ])
            ->find($group_id);

    }

    public function getGroupWithFiles2(int $group_id): ?Group
    {
        $group = Group::with([
            'owner:id,name,username,email',
            'files' => function ($q) {
                $q->select(['id', 'name', 'status', 'group_id', 'size'])->where('admin_group_approve', 'accepted');
            },
            'files.versions' => function ($q) {
                $q->select(['id', 'user_id', 'created_at', 'file_id', 'size'])->where('path', '!=', null)->orderBy('id', 'desc');
            },
        ])
            ->find($group_id);

        return $this->convertFileStatusToBooleanAndAddUser($group);

    }

    public function convertFileStatusToBooleanAndAddUser(Group $group): Group
    {
        foreach ($group->files as $file) {
            $file->statusBool = (bool)$file->status;
            foreach ($file->versions as $version) {
                // Fetch user data manually
                $user = $this->userRepository->getUserById2($version->user_id);
                $image_path = $this->userImageRepository->getUserImage($version->user_id);

                $userDTO = UserDTO::fromUser($user,url($image_path->path));

                $version->size = (double)$version->size;

                // Assign user object to the version
                $version->user = $userDTO;
                unset($version->user_id); // Optionally remove user_id if not needed
            }
        }

        return $group;
    }

    public function find(int $group_id): ?Group
    {
        return Group::find($group_id);
    }

    public function update(Group $group, array $array): void
    {
        $group->update($array);
    }

    public function delete(UserGroup $oldest_group_member): void
    {
        $oldest_group_member->delete();
    }

    public function editGroupName(int $group_id, string $name): Group
    {
        $group = $this->find($group_id);
        $group->name = $name;
        $group->save();

        return $group;
    }

    public function removeUsersFromGroup($groupId, array $ids): void
    {
        $group = $this->find($groupId);
        $group->users()->whereIn('user_id', $ids)->update(['group_id' => null]);
    }

}
