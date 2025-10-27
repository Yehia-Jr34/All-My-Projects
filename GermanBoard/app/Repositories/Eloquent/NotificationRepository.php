<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getUserNotifications(int $userId): array
    {
        return Notification::select('id', 'notification_type_id', 'text', 'created_at')
            ->where('user_id', $userId)
            ->with(['notificationType' => function ($query) {
                $query->select('id', 'icon', 'type');
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function storeNotification(int $userId, array $notification): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'notification_type_id' => $notification['notification_type_id'],
            'text' => $notification['text'],
        ]);
    }

    public function storeMultipleNotifications(array $notifications): void
    {
        Notification::insert($notifications);
    }
}
