<?php

namespace App\Repositories\Eloquent;

use App\Models\AddFileNotification;
use App\Repositories\Contracts\AddFileNotificationRepositoryInterface;
use Illuminate\Support\Collection;

class AddFileNotificationRepository implements AddFileNotificationRepositoryInterface
{
    public function getNotification(int $user_id): Collection
    {
        return AddFileNotification::select('notification_text', 'created_at')
            ->where('user_id', $user_id)
            ->get();
    }

    public function store(array $data): void
    {
        AddFileNotification::insert($data);
    }

    public function get(int $user_id, int $group_id, int $file_id): AddFileNotification
    {
       return AddFileNotification::where('user_id', $user_id)
            ->where('group_id', $group_id)
            ->where('file_id', $file_id)
            ->first();
    }

    public function fileAccepted(AddFileNotification $addFileNotification): void
    {
        $addFileNotification->update([
            'status' => 'accepted'
        ]);
    }

    public function fileRejected(AddFileNotification $addFileNotification): void
    {
        $addFileNotification->status = "rejected";
        $addFileNotification->save();
    }
}
