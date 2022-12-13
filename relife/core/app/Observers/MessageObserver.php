<?php

namespace App\Observers;

use App\Events\ChatMessageEvent;
use App\Exceptions\RegularException;
use App\Models\Chat\ChatMessage;
use App\Services\Chat\ChatMessageService;
use App\Services\Common\NotificationService;

class MessageObserver
{
    /**
     * @param ChatMessage $message
     * @return void
     * @throws RegularException
     */
    public function created(ChatMessage $message): void
    {
        $message->loadMissing([
            'user',
            'chat'
        ]);

        ChatMessageEvent::dispatch($message, ChatMessageEvent::STATE_CREATE);

        /** @var ChatMessageService $chatMessageService */
        $chatMessageService = resolve(ChatMessageService::class);
        $chatMessageService->setRead($message->chat);

        /** @var NotificationService $notificationService */
        $notificationService = resolve(NotificationService::class);
        $notificationService->generatePush($message);
    }

    /**
     * @param ChatMessage $message
     * @return void
     */
    public function deleting(ChatMessage $message): void
    {
        ChatMessageEvent::dispatch($message, ChatMessageEvent::STATE_DELETE);
    }
}
