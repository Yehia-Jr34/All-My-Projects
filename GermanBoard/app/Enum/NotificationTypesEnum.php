<?php

namespace App\Enum;

enum NotificationTypesEnum: string
{
    case SESSION_MODIFIED = 'session_modified';
    case SESSION_CANCELED = 'session_canceled';
    case SESSION_UPCOMING = 'session_upcoming';
    case ATTACHMENT_ADDED = 'attachment_added';
    case PASSED_TRAINING = 'passed_training';
    case PAYMENT_FAILED = 'payment_failed';
    case PAYMENT_SUCCEEDED = 'payment_succeeded';
    case ENROLLED_IN_TRAINING = 'enrolled_in_training';
    case CERTIFICATE_READY = 'certificate_ready';

    // admin
    case TRAINING_ADDED = 'training_added';

    //Provider
    case NEW_REGISTRATION = 'new_registration';
    case MEMBERSHIP_EXPIRED='membership_expired';
    case MEMBERSHIP_EXPIRE_SOON = 'membership_expire_soon';
    case MEMBERSHIP_REVOKED='membership_revoked';

}
