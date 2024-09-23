<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels; // Added traits for better functionality

    public $data;

    public function __construct($data)
    {
        $this->data = $data; // Ensure this includes necessary info (e.g., type, post_id)
    }

    public function broadcastOn()
    {
        return new Channel('posts'); // Public channel; change to PrivateChannel if needed
    }

    public function broadcastAs()
    {
        return 'notification.sent'; // Event name for listeners
    }
}
