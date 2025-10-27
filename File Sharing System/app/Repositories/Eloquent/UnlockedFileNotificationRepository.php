<?php

namespace App\Repositories\Eloquent;

use App\Models\UnlockedFileNotification;
use App\Repositories\Contracts\UnlockedFileNotificationRepositoryInterface;
use Illuminate\Support\Collection;

class UnlockedFileNotificationRepository implements UnlockedFileNotificationRepositoryInterface
{
    public function getNotification(int $user_id): Collection
    {
        return UnlockedFileNotification::select('notification_text', 'created_at')
            ->where('user_id', $user_id)
            ->get();
    }

    public function store(array $data): void
    {
        UnlockedFileNotification::insert($data);
    }
}
