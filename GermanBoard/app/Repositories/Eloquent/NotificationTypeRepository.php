<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationType;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;

class NotificationTypeRepository implements NotificationTypeRepositoryInterface
{
    public function getByType(string $type): NotificationType
    {
        return NotificationType::where('type', $type)->first();
    }
}
