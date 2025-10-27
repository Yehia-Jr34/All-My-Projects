<?php

namespace App\Repositories\Eloquent;

use App\Models\LockedFileNotification;
use App\Repositories\Contracts\LockedFileNotificationRepositoryInterface;
use Illuminate\Support\Collection;

class LockedFileNotificationRepository implements LockedFileNotificationRepositoryInterface
{
    public function store(array $data): void
    {
        LockedFileNotification::insert($data);
    }

    public function getNotification(int $user_id): Collection
    {
        return LockedFileNotification::select('notification_text', 'created_at')
            ->where('user_id', $user_id)
            ->get();
    }
}
