<?php
namespace App\Console;

use App\Jobs\SendScheduledMessage;
use App\Models\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel {
    
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\ApiSrv'
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        
        $schedule->job(new SendScheduledMessage)
            ->when(function () {
                return Event::whereEnabled(1)->count() > 0;
            })->everyMinute();
        
        // $schedule->job(new GatherPassageLog)->everyMinute();
        
    }
    
    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        
        /** @noinspection PhpIncludeInspection */
        require base_path('routes/console.php');
        
    }
}
