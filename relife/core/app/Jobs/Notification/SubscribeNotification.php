<?php

namespace App\Jobs\Notification;

use App\Enums\NotificationEnum;
use App\Models\Common\Notification;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubscribeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly User $user,
        private readonly int $followUserId
    ) {
    }

    public function handle()
    {
        Notification::query()->create([
            'user_id' => $this->followUserId,
            'initial_user_id' => $this->user->id,
            'slug' => $this->user->followings->contains($this->followUserId)
                ? NotificationEnum::TYPE_SUBSCRIBE
                : NotificationEnum::TYPE_UNSUBSCRIBE
        ]);
    }
}
