<?php

namespace App\Enums;

enum FilesVersionsStatusEnum : string
{
    case PENDING = 'pending';

    case ACCEPTED = 'accepted';

    case REJECTED = 'rejected';
}
