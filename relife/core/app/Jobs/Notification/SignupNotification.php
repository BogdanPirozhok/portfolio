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

class SignupNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly User $user,
        private readonly User $initialUser
    ) {
    }

    public function handle()
    {
        Notification::query()->create([
            'user_id' => $this->user->id,
            'initial_user_id' => $this->initialUser->id,
            'slug' => NotificationEnum::TYPE_NEW_REGISTRATION
        ]);
    }
}
