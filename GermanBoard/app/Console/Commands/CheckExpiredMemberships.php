<?php

namespace App\Console\Commands;

use App\Enum\AppConstants;
use App\Enum\NotificationTypesEnum;
use App\Models\MemberShip;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for providers with expired memberships and notify them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $allNotificationData = [];

        // 1. Handle memberships that expired today
        $membershipsEndToday = MemberShip::with(['provider.user'])
            ->whereDate('expired_at', now()->toDateString())
            ->get();

        if($membershipsEndToday->count() > 0){
            // Notifications for providers
            $todayNotifications = $membershipsEndToday->map(function ($membership){
                return [
                    'user_id' => $membership->provider->user->id,
                    'title' => 'Membership Expired',
                    'body' => 'Your membership has expired. Please renew it to continue managing your account.',
                    'notification_type' => NotificationTypesEnum::MEMBERSHIP_EXPIRED->value,
                    'is_read' => false,
                    'is_seen' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            // Notifications for admin
            $admin = User::where('email', AppConstants::ADMIN_EMAIL->value)->first();
            if ($admin) {
                $adminNotifications = $membershipsEndToday->map(function ($membership) use($admin) {
                    $providerEmail = $membership->provider?->user?->email;
                    return [
                        'user_id' => $admin->id,
                        'title' => 'Membership Expired',
                        'body' => "Provider: $providerEmail membership has expired",
                        'notification_type' => NotificationTypesEnum::MEMBERSHIP_EXPIRED->value,
                        'is_read' => false,
                        'is_seen' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                })->toArray();

                $todayNotifications = array_merge($todayNotifications, $adminNotifications);
            }

            $allNotificationData = array_merge($allNotificationData, $todayNotifications);
        }

        // 2. Handle memberships expiring soon (5 days or less)
        $membershipsExpiringSoon = MemberShip::with(['provider.user'])
            ->where('expired_at', '>', now()) // Not expired yet
            ->where('expired_at', '<=', now()->addDays(5)) // Will expire within 5 days
            ->get();

        if($membershipsExpiringSoon->count() > 0){
            // Notifications for providers
            $soonNotifications = $membershipsExpiringSoon->map(function ($membership){
                $daysLeft = (int)now()->diffInDays(Carbon::parse($membership->expired_at));

                return [
                    'user_id' => $membership->provider->user->id,
                    'title' => 'Membership Expiring Soon',
                    'body' => "Your membership will expire in $daysLeft days. Please renew it to avoid service interruption.",
                    'notification_type' => NotificationTypesEnum::MEMBERSHIP_EXPIRE_SOON->value,
                    'is_read' => false,
                    'is_seen' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            // Notifications for admin (optional)
            $admin = User::where('email', AppConstants::ADMIN_EMAIL->value)->first();
            if ($admin) {
                $adminSoonNotifications = $membershipsExpiringSoon->map(function ($membership) use($admin) {
                    $providerEmail = $membership->provider->user->email;
                    $daysLeft = (int)now()->diffInDays(Carbon::parse($membership->expired_at));

                    return [
                        'user_id' => $admin->id,
                        'title' => 'Membership Expiring Soon',
                        'body' => "Provider: $providerEmail membership will expire in $daysLeft days",
                        'notification_type' => NotificationTypesEnum::MEMBERSHIP_EXPIRE_SOON->value,
                        'is_read' => false,
                        'is_seen' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                })->toArray();

                $soonNotifications = array_merge($soonNotifications, $adminSoonNotifications);
            }

            $allNotificationData = array_merge($allNotificationData, $soonNotifications);
        }

        // 3. Insert all notifications at once
        if(!empty($allNotificationData)){
            Notification::insert($allNotificationData);
        }

        // Optional: Log the results
        $this->info("Processed {$membershipsEndToday->count()} expired memberships and {$membershipsExpiringSoon->count()} expiring soon memberships.");
        $this->info("Created " . count($allNotificationData) . " notifications.");
    }
}
