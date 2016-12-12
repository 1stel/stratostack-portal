<?php namespace App\Console;

use App\Activity;
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
        'App\Console\Commands\BillRun',
        'App\Console\Commands\BillGetRecords',
        'App\Console\Commands\BillGetPricing'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();

        // Delete user activity older than 30 days
        $schedule->call(function () {
            Activity::where('created_at', '<', date('Y-m-d H:i:s', strtotime("-1 month")))->delete();
        })->daily();

        $schedule->command('bill:run')->daily();
    }
}
