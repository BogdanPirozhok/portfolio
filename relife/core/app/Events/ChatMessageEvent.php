<?php

namespace App\Events;

use App\Http\Resources\Chat\ChatMessageResource;
use App\Models\Chat\ChatMessage;
use App\Models\User\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const STATE_CREATE = 1;
    const STATE_DELETE = 2;

    protected User $user;

    /**
     * @param ChatMessage $message
     * @param int $state
     */
    public function __construct(
        private readonly ChatMessage $message,
        private readonly int $state
    ) {
        $this->user = $this->message->getRecipient();
    }

    public function broadcastOn()
    {
        return 'chats.' . $this->user->id;
    }

    public function broadcastAs()
    {
        return 'chats.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => ChatMessageResource::make($this->message),
            'state' => $this->state,
            'all_count_unread_notices' => $this->user->getUnreadNotificationsCount(),
        ];
    }
}
