<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function accept(User $user, Invitation $invitation): bool
    {
        // Check if the authenticated user is the owner of the invitation
        return $invitation->group_member_id === $user->id;
    }
}
