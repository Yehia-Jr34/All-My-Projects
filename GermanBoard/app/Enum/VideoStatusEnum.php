<?php

namespace App\Enum;

enum VideoStatusEnum: string
{
    case NOT_WATCHED = 'not watched';
    case IN_PROGRESS = 'in progress';
    case COMPLETED = 'completed';
    case LOCKED = 'locked';

}
