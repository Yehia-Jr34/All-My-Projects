<?php

namespace App\Enum\Complaint;

enum ComplaintStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in progress';
    case CLOSED = 'closed';
    case REJECTED = 'rejected';
    case RESOLVED = 'resolved';
}
