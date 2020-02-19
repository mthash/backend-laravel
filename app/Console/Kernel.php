<?php

namespace App\Console;

use App\Console\Commands\HistoryTask;
use App\Console\Commands\MiningFluctuateTask;
use App\Console\Commands\MiningStartTask;
use App\Console\Commands\SeederTask;
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
        MiningFluctuateTask::class,
        MiningStartTask::class,
        HistoryTask::class,
        SeederTask::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('hash:quotes')->hourly();
//        $schedule->command('hash:watch')->everyTenMinutes();
//        $schedule->command('hash:start')->everyMinute();
//        $schedule->command('hash:fluctuate')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
