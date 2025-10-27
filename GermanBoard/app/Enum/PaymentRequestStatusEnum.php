<?php

namespace App\Enum;

enum PaymentRequestStatusEnum: string
{
    case PENDING = 'PENDING';

    case APPROVED = 'APPROVED';

}
