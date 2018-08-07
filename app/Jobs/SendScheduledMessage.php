<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Models\Event;
use App\Models\Message;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SendScheduledMessage - 发送定时消息
 * @package App\Jobs
 */
class SendScheduledMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobTrait;
    
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
     * @return bool
     * @throws Throwable
     */
    public function handle() {

        try {
            DB::transaction(function () {
                $events = Event::whereEnabled(1)
                    ->where('start', '<=', date(now()))
                    ->take(500)->get();
                foreach ($events as $event) {
                    $message = Message::whereEventId($event->id)->first();
                    if (!$message) { continue; }
                    $sent = $this->sendMessage($message);
                    if ($sent) { $event->update(['enabled' => 0]); }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
