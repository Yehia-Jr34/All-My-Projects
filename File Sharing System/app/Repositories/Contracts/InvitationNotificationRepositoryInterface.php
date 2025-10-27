<?php

namespace App\Repositories\Contracts;

use App\Models\InvitationNotification;
use Illuminate\Support\Collection;

interface InvitationNotificationRepositoryInterface
{
    public function store(array $data): void;

    public function get(int $group_id, int $user_id): InvitationNotification;
    public function invitationAccepted(InvitationNotification $invitationNotification): void;
    public function invitationRejected(InvitationNotification $invitationNotification): void;

    public function getNotification(int $user_id): Collection;
}
