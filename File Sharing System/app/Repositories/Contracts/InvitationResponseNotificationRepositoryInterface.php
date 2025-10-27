<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface InvitationResponseNotificationRepositoryInterface
{
    public function store(array $data): void;

    public function getNotification(int $user_id): Collection;
}
