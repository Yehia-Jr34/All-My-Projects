<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface UnlockedFileNotificationRepositoryInterface
{
    public function store(array $data): void;
    public function getNotification(int $user_id): Collection;
}
