<?php

namespace App\Repositories\Contracts;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Collection;

interface InvitationRepositoryInterface {

    public function createInvitations(array $invitationsData) : void;

    public function findById(int $invitation_id) : ?Invitation ;

    public function accept(Invitation $invitation) : void;

    public function reject(Invitation $invitation) : void;

    public function getInvitations(int $user_id): Collection;

    public function getInvitationsForUsersToGroup(array $user_ids , int $group_id): Collection;


}
