<?php

namespace App\Services\Notification;

use App\Repositories\Contracts\AddFileNotificationRepositoryInterface;
use App\Repositories\Contracts\AddFileNotificationResponseRepositoryInterface;
use App\Repositories\Contracts\InvitationNotificationRepositoryInterface;
use App\Repositories\Contracts\InvitationResponseNotificationRepositoryInterface;
use App\Repositories\Contracts\LockedFileNotificationRepositoryInterface;
use App\Repositories\Contracts\UnlockedFileNotificationRepositoryInterface;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function __construct(
        protected InvitationNotificationRepositoryInterface         $invitationNotificationRepository,
        protected InvitationResponseNotificationRepositoryInterface $invitationResponseNotificationRepository,
        protected AddFileNotificationResponseRepositoryInterface    $addFileNotificationResponseRepository,
        protected AddFileNotificationRepositoryInterface            $addFileNotificationRepository,
        protected LockedFileNotificationRepositoryInterface         $lockedFileNotificationRepository,
        protected UnlockedFileNotificationRepositoryInterface       $unlockedFileNotificationRepository,

    ) {}

    public function get(): array
    {
        $user_id = Auth::user()->id;
        $collection1 = $this->invitationNotificationRepository->getNotification($user_id);
        $collection2 = $this->invitationResponseNotificationRepository->getNotification($user_id);
        $collection3 = $this->lockedFileNotificationRepository->getNotification($user_id);
        $collection5 = $this->addFileNotificationResponseRepository->getNotification($user_id);
        $collection6 = $this->addFileNotificationRepository->getNotification($user_id);
        $collection4 = $this->unlockedFileNotificationRepository->getNotification($user_id);

        $merged_collection = $collection1
            ->concat($collection2->toArray())
            ->concat($collection3->toArray())
            ->concat($collection4->toArray())
            ->concat($collection5->toArray())
            ->concat($collection6->toArray());

        // Convert to array and reindex to get sequential numeric keys
        return array_values($merged_collection->sortByDesc('created_at')->toArray());
    }
}
