<?php

namespace App\Observers;

use App\Events\SendNotificationEvent;
use App\Exceptions\RegularException;
use App\Models\Common\Notification;
use App\Services\Common\NotificationService;

class NotificationObserver
{
    /**
     * @param Notification $notification
     * @return void
     * @throws RegularException
     */
    public function created(Notification $notification): void
    {
        $notification->loadMissing([
            'post',
            'user',
            'user.profile',
        ]);

        if ($notification->user_id) {
            SendNotificationEvent::dispatch($notification);
        }

        /** @var NotificationService $notificationService */
        $notificationService = resolve(NotificationService::class);
        $notificationService->generatePush($notification);
    }
}
