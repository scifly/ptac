<?php
namespace App\Jobs;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class TestJob
 * @package App\Jobs
 */
class TestJob implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        
        Event::create([
            'title' => '37289',
            'remark' => 'remark',
            'location' => '437289',
            'contact' => '777',
            'url' => 'http://',
            'start' => '2018-03-20 12:35:40',
            'end' => '2018-03-21 12:35:40',
            'ispublic' => 0,
            'iscourse' => 0,
            'educator_id' => 1,
            'subject_id' => 2,
            'alertable' => 1,
            'alert_mins' => 10,
            'user_id' => 1,
            'enabled' => 1
        ]);
        
    }
}
