<?php

namespace App\Repositories\Contracts;

use App\Models\AddFileNotification;
use Illuminate\Support\Collection;

interface AddFileNotificationRepositoryInterface
{
    public function getNotification(int $user_id): Collection;
    public function store(array $data): void;

    public function get(int $user_id, int $group_id, int $file_id): AddFileNotification;
    public function fileAccepted(AddFileNotification $addFileNotification): void;
    public function fileRejected(AddFileNotification $addFileNotification): void;
}
