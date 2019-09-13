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
use Throwable;

/**
 * Class SendScheduledMessage - 发送定时消息
 * @package App\Jobs
 */
class SendScheduledMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, JobTrait;
    
    /**
     * Execute the job.
     *
     * @return bool
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $events = Event::where([
                    ['enabled', '=', 1],
                    ['start', '<=', date(now())]
                ])->take(500)->get();
                foreach ($events as $event) {
                    $message = Message::whereEventId($event->id)->first();
                    if (!$message) continue;
                    # todo -
                    if ($this->send($message)) $event->delete();
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param Exception $e
     * @throws Exception
     */
    function failed(Exception $e) {
    
        $this->eHandler($this, $e);
        
    }
    
}
