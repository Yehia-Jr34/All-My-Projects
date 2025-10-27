<?php

namespace App\Jobs;

use App\Enum\NotificationTypesEnum;
use App\Services\Notification\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $userIds,
        public string $title,
        public string $body,
        public NotificationTypesEnum $notificationType,
        public ?string $image = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FirebaseNotificationService $notificationService): void
    {
        Log::info('Sending notification: ' . $this->title . ' to ' . implode(', ', $this->userIds));

        $notificationService->sendNotification($this->userIds, $this->title, $this->body, $this->notificationType, $this->image);
    }
}
