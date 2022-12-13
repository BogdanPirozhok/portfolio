<?php

namespace App\Providers;

use App\Models\Chat\ChatMessage;
use App\Models\Common\Notification;
use App\Models\Common\Rating;
use App\Models\Post\Post;
use App\Models\User\Profile;
use App\Observers\MessageObserver;
use App\Observers\NotificationObserver;
use App\Observers\PostObserver;
use App\Observers\RatingObserver;
use App\Observers\User\ProfileObserver;
use App\Observers\User\UserInterestsPivotObserver;
use App\Observers\User\UserUsefulsPivotObserver;
use App\Pivots\User\UserInterestsPivot;
use App\Pivots\User\UserUsefulsPivot;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Profile::observe(ProfileObserver::class);

        Rating::observe(RatingObserver::class);

        Notification::observe(NotificationObserver::class);

        UserUsefulsPivot::observe(UserUsefulsPivotObserver::class);
        UserInterestsPivot::observe(UserInterestsPivotObserver::class);

        ChatMessage::observe(MessageObserver::class);

        Post::observe(PostObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
