<?php

namespace App\Enum;

enum ArticlesStatusEnum: string
{
    case APPROVED = 'APPROVED';
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';

}
