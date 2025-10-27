<?php

namespace App\Enum;

enum SessionStatusEnum: string
{
    case NOT_STARTED = 'not_started';
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';
    case CANCELLED = 'canceled';
}
