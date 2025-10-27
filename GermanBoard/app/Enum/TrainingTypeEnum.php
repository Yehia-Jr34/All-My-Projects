<?php

namespace App\Enum;

enum TrainingTypeEnum: string
{
    case LIVE = 'live';
    case RECORDED = 'recorded';
    case ONSITE = 'onsite';
}
