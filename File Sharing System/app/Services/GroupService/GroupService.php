<?php

namespace App\Services\GroupService;

use App\DTOs\Group\CreateGroupDTO;
use App\DTOs\Group\GroupDTO;
use App\DTOs\User\UserDTO;
use App\Enums\FilesVersionsStatusEnum;
use App\Enums\StatusCodeEnum;
use App\Events\InvitationNotificationEvent;
use App\Models\Group;
use App\Models\UserReport;
use App\Repositories\Contracts\FileRepositoryInterface;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\InvitationNotificationRepositoryInterface;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use App\Repositories\Contracts\UserReportRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class GroupService
{
    public function __construct(
        protected GroupRepositoryInterface                  $groupRepository,
        protected InvitationRepositoryInterface             $invitationRepository,
        protected UserRepositoryInterface                   $userRepository,
        protected UserGroupRepositoryInterface              $userGroupRepository,
        protected FileRepositoryInterface                   $fileRepository,
        protected InvitationNotificationRepositoryInterface $invitationNotificationRepository,
        protected UserReportRepositoryInterface             $userReportRepository,
    ) {}

    public function create() {}

    public function createGroupWithMembers(CreateGroupDTO $createGroupDTO): void
    {
        DB::transaction(function () use ($createGroupDTO) {

            $created_by = auth()->user()->id;

            $groupDTO = $this->groupRepository
                ->create($createGroupDTO->name, $createGroupDTO->description, $created_by);

            if ($createGroupDTO->members) {

                $invitationsData = collect($createGroupDTO->members)
                    ->map(function ($item) use ($created_by, $groupDTO) {
                        return [
                            'admin_group_id' => $created_by,
                            'group_member_id' => $item,
                            'group_id' => $groupDTO->id,
                            'status' => FilesVersionsStatusEnum::PENDING->value
                        ];
                    });
                $this->invitationRepository->createInvitations($invitationsData->toArray());

                //store invitation notification
                $invitationNotificationData = collect($createGroupDTO->members)
                    ->map(function ($item) use ($groupDTO) {
                        return [
                            'user_id' => $item,
                            'group_id' => $groupDTO->id,
                            'notification_text' => $groupDTO->owner_name . " has invited you to the group : " . $groupDTO->group_name,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    });

                $this->invitationNotificationRepository->store($invitationNotificationData->toArray());
                foreach ($invitationNotificationData as $item) {
                    event(new InvitationNotificationEvent($item['user_id'], $item['notification_text']));
                }
            }
        });
    }

    public function show(int $group_id): Group
    {
        $group = $this->groupRepository->getGroupWithMembers($group_id);

        if (!$group) {

            throw new \DomainException('group not found', StatusCodeEnum::NOT_FOUND->value);
        }

        $this->ensureUserCanViewGroup($group);


        return $group;
    }

    public function showWithfiles(int $group_id): Group
    {
        $group = $this->groupRepository->getGroupWithFiles2($group_id);

        $this->ensureUserCanViewGroup($group);

        if (!$group) {

            throw new \DomainException('group not found', StatusCodeEnum::NOT_FOUND->value);
        }

        return $group;
    }

    public function exitGroup(int $group_id): void
    {
        // ensure that group exist
        $group = $this->validateGroup($group_id);

        $user = auth()->user();

        // handle the owner case
        if ($group->created_by === $user->id) {

            $oldest_group_member = $this->userGroupRepository->findOldestMember($group->id);

            if ($oldest_group_member) {

                $this->groupRepository->update($group, ["created_by" => $oldest_group_member->user_id]);

                $this->userGroupRepository->delete($oldest_group_member);

                return;
            } else {

                $this->groupRepository->update($group, ["created_by" => null]);

                return;
            }
        }

        // handle the member case
        $group_member = $this->userGroupRepository->findUserGroup($group->id, $user->id);

        if ($group_member) {

            $this->userGroupRepository->delete($group_member);

            return;
        }

        throw new \DomainException('user not in the group');
    }

    // Helpers Methods

    public function ensureUserCanViewGroup(Group $group): void
    {
        $user = auth()->user();

        if (!$user->can('isMember', $group) && !$user->can('isAdmin', $group) && !$user->can('ownsGroup', $group)) {

            throw new \DomainException('You are not authorized to view this group.');
        }
    }

    private function validateGroup(int $group_id): Group
    {

        $group = $this->groupRepository->find($group_id);

        if (!$group) {

            throw new \DomainException('group not found');
        }

        return $group;
    }

    public function edit_group_name(int $group_id, string $groupName): GroupDTO
    {
        $user = Auth::user();
        $user_id = $user->id;
        $group = $this->groupRepository->getGroupById($group_id);
        if ($this->userIsAnAdmin($group->created_by, $user_id)) {
            $groupDTO = GroupDTO::fromModel($this->groupRepository->editGroupName($group_id, $groupName));
            return $groupDTO;
        }
        throw new Exception("you are not authorized tho process this action");
    }

    public function removeMember(int $group_id, array $ids): string
    {
        $group = $this->groupRepository->find($group_id);
        $user = Auth::user();

        if ($this->userIsAnAdmin($group->created_by, $user->id)) {
            $this->userGroupRepository->deleteMembers($group_id, $ids);
            return "users removed";
        } else {
            throw new Exception("you are not authorized tho process this action");
        }
    }

    private function userIsAnAdmin(int $group_created_by, int $user_id): bool
    {
        return $group_created_by == $user_id;
    }

    public function getUserReport(int $group_id): Collection
    {
        $ids = $this->userGroupRepository->getMembersIds($group_id);
        $group = $this->groupRepository->getGroupById($group_id);
        $user_id = auth()->id();
        if ($this->userIsAnAdmin($user_id, $group->created_by)) {
            return $this->userReportRepository->get($group_id);
        } else {
            throw new Exception("you are not authorized tho process this action");
        }
    }
}
