<?php

namespace App\Enum;

enum RolesEnum: string
{
    case TRAINEE = 'trainee';
    case TRAINER = 'trainer';
    case ADMIN = 'admin';
    case AGENT = 'agent';
    case CENTER = 'center';
    case INTERNAL_TRAINER = 'internal_trainer';

}
