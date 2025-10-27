<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSentMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id, $doctor_id, $message, $conv_id;

    /**
     * Create a new event instance.
     */
    public function __construct($user_id, $doctor_id, $message, $conv_id)
    {
        $this->doctor_id = $doctor_id;
        $this->user_id = $user_id;
        $this->message = $message;
        $this->conv_id = $conv_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('UserSentMessage-channel'),
        ];
    }


}
