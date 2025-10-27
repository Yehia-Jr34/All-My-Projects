<?php

namespace App\Services\Notification;

use App\Enum\NotificationTypesEnum;
use App\Enum\StatusCodeEnum;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;

class FirebaseNotificationService
{
    private $messaging;

    public function __construct(
        public NotificationRepositoryInterface $notificationRepository,
        public NotificationTypeRepositoryInterface $notificationTypeRepository,
        public UserRepositoryInterface $userRepository,
    ) {
        $this->messaging = (new Factory)->withServiceAccount(storage_path('firebase-auth.json'))
            ->createMessaging();
    }

    public function sendNotification(array $userIds, string $title, string $body, NotificationTypesEnum $notificationType, ?string $image = null)
    {
        DB::transaction(function () use ($userIds, $title, $body, $image, $notificationType) {
            try {

                $tokens = $this->userRepository->getDeviceTokens($userIds);

                $insertedNotifications = collect($userIds)->map(function ($userId) use ($title, $body, $notificationType) {
                    return [
                        'title' => $title,
                        'body' => $body,
                        'notification_type' => $notificationType->value,
                        'user_id' => $userId,
                        'is_seen' => false,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

                $this->notificationRepository->storeMultipleNotifications($insertedNotifications->toArray());

                $notification = Notification::create(
                    $title,
                    $body,
                    $image
                );

                $message = CloudMessage::new()
                    ->withNotification($notification);

                Log::info($tokens );

                $this->messaging->sendMulticast($message, $tokens);

                Log::info('Firebase send notification: ' . $title . ' to ' . implode(', ', $tokens));

            }


            catch (\Exception $e) {
                Log::error('Firebase send error: ' . $e);
            }
        });
    }
}
