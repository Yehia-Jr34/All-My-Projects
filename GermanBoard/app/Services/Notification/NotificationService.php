<?php

namespace App\Services\Notification;

use App\Models\Notification;

class NotificationService
{

    public function getMyProviderNotification()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id' , $user->id)->orderBy('id' , 'DESC')->get();
        Notification::where('user_id' , $user->id)->where('is_read' ,false)->update(['is_read'=>true]);
        return $notifications;
    }

    public function isThereUnreadNotifications()
    {
        $user = auth()->user();
        $notification_not_read = Notification::where('user_id' , $user->id)
            ->where('is_read' , false)->get()->count();

        return $notification_not_read == 0 ? false : true;
    }
}
