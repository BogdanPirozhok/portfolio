<?php

namespace App\Console;

use App\Enums\EnvironmentEnum;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $mainStageDomains = ['ojowo.co', 'ojowo.fun'];

        if (app()->environment(EnvironmentEnum::STAGE)
            && !in_array(config('app.domain'), $mainStageDomains)
        ) {
            return;
        }

        $schedule->command('cabinet:statistics')->everyMinute()->onOneServer();
        $schedule->command('email:distribution')->everyMinute()->onOneServer();
        $schedule->command('webinar:status-update')->everyMinute()->onOneServer();
        $schedule->command('webinar:send-link')->everyMinute()->onOneServer();
        $schedule->command('cabinet:activate-domain')->everyMinute()->onOneServer();

        $schedule->command('horizon:snapshot')->everyFiveMinutes()->onOneServer();
        $schedule->command('queue:monitor redis:emails,redis:marketing --max=5')->everyFiveMinutes()->onOneServer();
        $schedule->command('flow:update-contact-list-segment')->everyFiveMinutes()->onOneServer();
        $schedule->command('webinar:check-data-youtube')->everyFiveMinutes()->onOneServer();
        $schedule->command('sync:offer-activity')->everyFiveMinutes()->onOneServer();
        $schedule->command('sync:product-users')->everyFiveMinutes()->onOneServer();
        $schedule->command('cabinet:site-delete')->everyFiveMinutes()->onOneServer();

        $schedule->command('file:remove-unused-files')->everyTenMinutes()->onOneServer();
        $schedule->command('courses:notify-accessible-students')->everyTenMinutes()->onOneServer();
        $schedule->command('order:process-partial-payment')->everyTenMinutes()->onOneServer();

        $schedule->command('currency:update')->hourly()->onOneServer();
        $schedule->command('email:check-amount')->hourly()->onOneServer();

        if (!app()->isProduction()) {
            $schedule->command('subscriptions:check')->everyFiveMinutes()->onOneServer();
            $schedule->command('invoices:process')->everyFifteenMinutes()->onOneServer();
        }

        $schedule->command('timezones:update')->daily()->onOneServer();
        $schedule->command('check-mail-domain')->daily()->onOneServer();
        $schedule->command('integration:validate-zoom-account')->daily()->onOneServer();

        // $schedule->command('telescope:prune')->everySixHours()->onOneServer();
        $schedule->command('telescope:truncate')->daily()->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
