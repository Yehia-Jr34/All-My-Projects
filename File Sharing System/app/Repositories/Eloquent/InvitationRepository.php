<?php

namespace App\Repositories\Eloquent;

use App\Enums\FilesVersionsStatusEnum;
use App\Models\Invitation;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InvitationRepository implements InvitationRepositoryInterface {

    public function createInvitations(array $invitationsData): void
    {
        Invitation::insert($invitationsData);
    }

    public function findById(int $invitation_id): ?Invitation
    {
        return Invitation::find($invitation_id);
    }

    public function accept(Invitation $invitation): void
    {
        $invitation->update([
            'status' => FilesVersionsStatusEnum::ACCEPTED->value
        ]);
    }

    public function reject(Invitation $invitation): void
    {
        $invitation->update([
            'status' => FilesVersionsStatusEnum::REJECTED->value
        ]);
    }

    public function getInvitations(int $user_id): Collection
    {
        return Invitation::where('group_member_id', $user_id)
            ->where('status', FilesVersionsStatusEnum::PENDING->value)
            ->get();
    }

    public function getInvitationsForUsersToGroup(array $user_ids, int $group_id): Collection
    {
        return Invitation::with('user:id,username')->whereIn('group_member_id', $user_ids)->where('group_id',$group_id)->get();
    }
}
