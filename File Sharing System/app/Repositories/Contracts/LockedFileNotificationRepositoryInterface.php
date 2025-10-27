<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface LockedFileNotificationRepositoryInterface
{
    public function getNotification(int $user_id): Collection;
    public function store(array $data): void;
}
