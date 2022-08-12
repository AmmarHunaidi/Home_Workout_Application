<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $id, $user_id, $message, $chat_id, $created_at;
    public function __construct($id, $user_id, $chat_id, $message, $created_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->chat_id = $chat_id;
        $this->message = $message;
        $this->created_at = $created_at;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'chat_id' => $this->chat_id,
            'created_at' => (string)$this->created_at,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('chat');
    }
}
