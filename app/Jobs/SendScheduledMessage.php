<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Models\{Event, Message};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB};
use Pusher\PusherException;
use Throwable;

/**
 * Class SendScheduledMessage - 发送定时消息
 * @package App\Jobs
 */
class SendScheduledMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, JobTrait;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    function __construct() {
        //
    }
    
    /**
     * Execute the job.
     *
     * @return bool
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $events = Event::whereEnabled(1)
                    ->where('start', '<=', date(now()))
                    ->take(500)->get();
                foreach ($events as $event) {
                    $message = Message::whereEventId($event->id)->first();
                    if (!$message) continue;
                    $sent = $this->send($message);
                    if ($sent) $event->update(['enabled' => 0]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
    
        $this->eHandler($exception);
        
    }
    
}
