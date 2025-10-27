<?php

namespace App\Repositories\Eloquent;

use App\Models\AddFileNotification;
use App\Models\AddFileNotificationResponse;
use App\Repositories\Contracts\AddFileNotificationResponseRepositoryInterface;

class AddFileNotificationResponseRepository implements AddFileNotificationResponseRepositoryInterface
{
    public function store(array $data): void
    {
        AddFileNotificationResponse::insert($data);
    }

    public function getNotification(int $user_id): \Illuminate\Support\Collection
    {
        return AddFileNotification::select('notification_text', 'created_at')
            ->where('user_id', $user_id)
            ->get();
    }
}
