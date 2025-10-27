<?php

namespace App\Enum\Complaint;

enum ComplaintTypeEnum: string
{
    case TECHNICAL = 'technical';
    case PAYMENT = 'payment';
    case CONTENT = 'content';
    case OTHER = 'other';
}
