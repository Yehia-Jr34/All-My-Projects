<?php

namespace App\Repositories\Eloquent;

use App\Models\InvitationResponseNotification;
use App\Repositories\Contracts\InvitationResponseNotificationRepositoryInterface;
use Illuminate\Support\Collection;

class InvitationResponseNotificationRepository implements InvitationResponseNotificationRepositoryInterface
{

    public function store(array $data): void
    {
        InvitationResponseNotification::insert($data);
    }

    public function getNotification(int $user_id): Collection
    {
        return InvitationResponseNotification::select('notification_text', 'created_at')
            ->where('user_id', $user_id)
            ->get();
    }
}
