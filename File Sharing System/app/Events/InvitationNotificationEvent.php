<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $user_id;

    public mixed $data;


    /**
     * Create a new event instance.
     */
    public function __construct(int $user_id, mixed $data)
    {
        $this->user_id = $user_id;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'invitation',
            'data' => $this->data
        ];
    }

    public function broadcastAs(): string
    {
        return 'invitation';
    }
}
