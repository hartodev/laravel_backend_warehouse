<?php
namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public ChatMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.' . $this->message->chat_id)];
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->message->id,
            'chat_id'    => $this->message->chat_id,
            'sender_id'  => $this->message->sender_id,
            'message'    => $this->message->message,
            'is_read'    => $this->message->is_read,
            'created_at' => $this->message->created_at->toIso8601String(),
            'sender' => [
                'id'   => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'role' => $this->message->sender->role,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
