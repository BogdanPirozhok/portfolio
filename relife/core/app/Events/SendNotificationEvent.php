<?php

namespace App\Events;

use App\Contracts\Notificationable;
use App\Models\Common\Notification;
use App\Services\User\ProfileService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Notification $notification
     */
    public function __construct(
        private readonly Notification $notification
    ) {
    }

    public function broadcastOn()
    {
        return 'notice.' . $this->notification->user_id;
    }

    public function broadcastAs()
    {
        return 'notice.updated';
    }

    public function broadcastWith(): array
    {
        $data = $this->notification->recipient->getUnreadNotifications();

        return [
            'all_count_unread_notices' => $data['sum_all_count'],
            'has_unread_notices' => !empty($data[Notificationable::COMMON_NOTICE_TYPE])
        ];
    }
}
