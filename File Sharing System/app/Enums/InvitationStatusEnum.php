<?php

namespace App\Enums;

enum InvitationStatusEnum : string
{
    case PENDING = 'pending';

    case ACCEPTED = 'accepted';

    case REJECTED = 'rejected';
}
