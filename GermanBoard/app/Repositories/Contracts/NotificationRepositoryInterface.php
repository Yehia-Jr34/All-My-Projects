<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;

interface NotificationRepositoryInterface
{
    public function getUserNotifications(int $userId): array;

    public function storeNotification(int $userId, array $notification): Notification;

    public function storeMultipleNotifications(array $notifications): void;
}
