<?php
namespace App\Events;

use Auth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ContactSyncTrigger implements ShouldBroadcast {
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data;
    
    /**
     * Create a new event instance.
     *
     * @param $data
     */
    public function __construct($data) {
        
        $this->data = $data;
        
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn() {
        
        Log::debug('realname: ' . Auth::user()->realname);
        return new PrivateChannel('user.' . $this->data['userId']);
        
    }
    
}
