<?php

namespace App\Repositories\Contracts;

use App\Models\NotificationType;

interface NotificationTypeRepositoryInterface
{
    public function getByType(string $type): NotificationType;
}
