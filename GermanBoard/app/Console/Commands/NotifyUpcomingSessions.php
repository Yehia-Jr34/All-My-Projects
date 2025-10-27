<?php

namespace App\Console\Commands;

use App\Enum\NotificationTypesEnum;
use App\Models\Notification;
use App\Models\TrainingSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyUpcomingSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to trainees for sessions starting in the next 5 minutes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $fiveMinutesLater = $now->copy()->addMinutes(5);

        // Get sessions starting within the next 5 minutes and not already started
        $sessions = TrainingSession::with("training.training_trainees.trainee.user")
            ->where('upcoming_notification_sent',0)
            ->where('status', 'NOT_STARTED')
            ->whereBetween('start_date', [$now, $fiveMinutesLater])
            ->get();

        if(sizeof($sessions) === 0){
            return 0;
        }

        $notifications = [];
         $sessions->each(function ($session) use(&$notifications){
            $training_name =  $session->training->title_en;
            $session->training->training_trainees->each(function ($training_trainee) use(&$notifications , $session , $training_name) {
                $sessionName = $session->title;
                $notifications[] = [
                    'user_id' => $training_trainee->trainee->user_id,
                    'title' => "Session Upcoming",
                    'body' => "Your Session {$sessionName} from {$training_name} training is about to start after 5 minutes, Be Ready",
                    'notification_type' => NotificationTypesEnum::SESSION_UPCOMING->value,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            });
        });

         Notification::insert($notifications);

        foreach ($sessions as $session) {
            $session->update([
                'upcoming_notification_sent' => true
            ]);
        }

        return 0;
    }
}
