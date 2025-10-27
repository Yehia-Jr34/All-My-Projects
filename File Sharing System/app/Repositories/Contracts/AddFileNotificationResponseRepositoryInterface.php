<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AddFileNotificationResponseRepositoryInterface
{
    public function store(array $data): void;

    public function getNotification(int $user_id): Collection;
}
