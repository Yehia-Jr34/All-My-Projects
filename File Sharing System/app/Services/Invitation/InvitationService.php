<?php

namespace App\Services\Invitation;

use App\DTOs\Group\GroupDTO;
use App\DTOs\Invitation\InvitationDTO;
use App\DTOs\User\UserDTO;
use App\Enums\FilesVersionsStatusEnum;
use App\Enums\InvitationStatusEnum;
use App\Enums\RolesEnum;
use App\Events\InvitationNotificationEvent;
use App\Models\Group;
use App\Models\Invitation;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\InvitationNotificationRepositoryInterface;
use App\Repositories\Contracts\InvitationResponseNotificationRepositoryInterface;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use http\Client\Curl\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvitationService
{
    public function __construct(
        protected InvitationRepositoryInterface                     $invitationRepository,
        protected GroupRepositoryInterface                          $groupRepository,
        protected UserGroupRepositoryInterface                      $userGroupRepository,
        protected UserRepositoryInterface                           $userRepository,
        protected InvitationNotificationRepositoryInterface         $invitationNotificationRepository,
        protected InvitationResponseNotificationRepositoryInterface $invitationResponseNotificationRepository,
    ) {}

    public function createInvitation(array $data)
    {
        $created_by = auth()->user()->id;

        $group = $this->groupRepository->getGroupById($data['group_id']);

        $this->ensureUserCanManageGroup($group);

        $user = \auth()->user();

        $invitations = $this->invitationRepository->getInvitationsForUsersToGroup($data['members'], $data['group_id']);

        foreach ($invitations as $invitation) {

            if ($invitation->status === InvitationStatusEnum::ACCEPTED->value) {

                throw new \DomainException("user {$invitation->user->username} already accepted the invitation");
            }

            if ($invitation->status === InvitationStatusEnum::PENDING->value) {

                throw new \DomainException("user {$invitation->user->username} already been invited");
            }
        }

        foreach ($data['members'] as $member_id) {
            if ($member_id === $user->id) {
                throw  new \DomainException('you are the owner , you can\'t send invite to yourself');
            }
        }

        $invitationsData = collect($data['members'])->map(function ($item) use ($created_by, $data) {
            return [
                'admin_group_id' => $created_by,
                'group_member_id' => $item,
                'group_id' => $data['group_id'],
                'status' => FilesVersionsStatusEnum::PENDING->value,
                'created_at' => now()
            ];
        });
        $this->invitationRepository->createInvitations($invitationsData->toArray());

        //store invitation notification
        $invitationNotificationData = collect($data['members'])
            ->map(function ($item) use ($user, $group) {
                return [
                    'user_id' => $item,
                    'group_id' => $group->id,
                    'notification_text' => $user->name . " has invited you to the group : " . $group->name,
                    'created_at' => now(),
                ];
            });
        $this->invitationNotificationRepository->store($invitationNotificationData->toArray());

        foreach ($invitationNotificationData as $item) {
            event(new InvitationNotificationEvent($item['user_id'], $item['notification_text']));
        }
    }

    public function ensureUserCanManageGroup(Group $group): void
    {
        $user = auth()->user();

        if (!$user->can('ownsGroup', $group) && !$user->can('isAdmin', $group)) {

            throw new \DomainException('You are not authorized to manage this group.');
        }
    }

    public function accept(int $invitation_id)
    {
        DB::transaction(function () use ($invitation_id) {

            // find the invitation
            $invitation = $this->invitationRepository->findById($invitation_id);

            // if not throw exception
            if (!$invitation) {

                throw new \DomainException('not found', 404);
            }

            // check if the user owns the invitation
            $this->ensureUserCanAccept($invitation);

            // Check if the invitation is already accepted
            if ($invitation->status === FilesVersionsStatusEnum::ACCEPTED->value) {

                throw new \DomainException('This invitation has already been accepted.');
            }

            // Check if the invitation is already rejected
            if ($invitation->status === FilesVersionsStatusEnum::REJECTED->value) {

                throw new \DomainException('This invitation has already been rejected.');
            }

            // Accept Invitation by changing status
            $this->invitationRepository->accept($invitation);

            // Collect the UserGroup data
            $data = [
                'user_id' => $invitation->group_member_id,
                'group_id' => $invitation->group_id,
                'role' => RolesEnum::MEMBER->value
            ];

            // add the user to group by Create user group
            $this->userGroupRepository->create($data);

            //set invitation notification status to accepted
            $invitationNotification = $this->invitationNotificationRepository
                ->get($invitation->group_id, $invitation->group_member_id,);
            $this->invitationNotificationRepository->invitationAccepted($invitationNotification);

            //store invitation notification response
            $group = $this->groupRepository->getGroupById($invitation->group_id);
            $user = \auth()->user();
            $data = [
                'user_id' => $group->created_by,
                'group_id' => $group->id,
                'notification_text' => $user->name . " accepted your invitation to join the group : " . $group->name,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->invitationResponseNotificationRepository->store($data);
        });
    }


    public function reject(int $invitation_id)
    {
        DB::transaction(function () use ($invitation_id) {

            // find the invitation
            $invitation = $this->invitationRepository->findById($invitation_id);

            // if not throw exception
            if (!$invitation) {

                throw new \DomainException('not found', 404);
            }

            // check if the user owns the invitation
            $this->ensureUserCanAccept($invitation);

            // Check if the invitation is already accepted
            if ($invitation->status === FilesVersionsStatusEnum::ACCEPTED->value) {

                throw new \DomainException('This invitation has already been accepted.');
            }

            // Check if the invitation is already rejected
            if ($invitation->status === FilesVersionsStatusEnum::REJECTED->value) {

                throw new \DomainException('This invitation has already been rejected.');
            }

            // Reject Invitation by changing status
            $this->invitationRepository->reject($invitation);

            //set invitation notification status to rejected
            $invitationNotification = $this->invitationNotificationRepository
                ->get($invitation->group_id, $invitation->group_member_id,);
            $this->invitationNotificationRepository->invitationRejected($invitationNotification);

            //store invitation notification response
            $group = $this->groupRepository->getGroupById($invitation->group_id);
            $user = \auth()->user();
            $data = [
                'user_id' => $group->created_by,
                'group_id' => $group->id,
                'notification_text' => $user->name . " rejected your invitation to join the group : " . $group->name,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->invitationResponseNotificationRepository->store($data);
        });
    }


    // Helpers Methods

    public function ensureUserCanAccept(Invitation $invitation): void
    {
        $user = auth()->user();

        if (!$user->can('accept', $invitation)) {

            throw new \DomainException('You are not authorized to accept this invite.');
        }
    }

    public function getInvitations(int $user_id)
    {
        $invitations = $this->invitationRepository->getInvitations($user_id);
        //        return $invitations;
        $data = [];

        // create invitation data
        foreach ($invitations as $invitation) {
            $userDTO = $this->userRepository->getUserById($invitation->admin_group_id);
            $group = $this->groupRepository->getGroupDetails($invitation->group_id, $userDTO);

            $invitationDTO = InvitationDTO::create($invitation->id, $group, $invitation->status);

            $data[] = $invitationDTO;
        }
        return $data;
    }
}
