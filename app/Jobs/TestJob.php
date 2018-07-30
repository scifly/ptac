<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Models\Event;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
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
     * @return void
     */
    public function handle() {
        
        $events = Event::whereEnabled(1)->get();
        foreach ($events as $event) {
            if (date(now()) >= $event->start) {
                # send message immediately
                
            }
        }
    
    }
    
    private function message($eventId) {
        
        $message = Message::whereEventId($eventId)->first();
        $content = json_decode($message->content);
        list($users, $targets, $mobiles) = $message->targets(
            User::whereIn('userid', explode('|', $content->{'touser'}))->pluck('id')->toArray(),
            explode('|', $content->{'toparty'})
        );
        
        
    }
}
