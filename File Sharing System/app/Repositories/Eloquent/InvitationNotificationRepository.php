<?php

namespace App\Repositories\Eloquent;

use App\Models\InvitationNotification;
use App\Repositories\Contracts\InvitationNotificationRepositoryInterface;
use Illuminate\Support\Collection;

class InvitationNotificationRepository implements InvitationNotificationRepositoryInterface
{
    public function store(array $data): void
    {
        InvitationNotification::insert($data);
    }

    public function get(int $group_id, int $user_id): InvitationNotification
    {
        return InvitationNotification::select('id', 'user_id', 'group_id', 'status')
            ->where('group_id', $group_id)
            ->where('user_id', $user_id)
            ->first();
    }

    public function invitationAccepted(InvitationNotification $invitationNotification): void
    {
        $invitationNotification->status = "accepted";
        $invitationNotification->save();
    }

    public function invitationRejected(InvitationNotification $invitationNotification): void
    {
        $invitationNotification->status = "rejected";
        $invitationNotification->save();
    }

    public function getNotification(int $user_id): Collection
    {
        return InvitationNotification::select('notification_text', 'created_at')
            ->where('user_id', $user_id)
            ->get();
    }
}
