<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeleted {
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $data;
    
    /**
     * Create a new event instance.
     * @param array $data
     */
    public function __construct(array $data) {
        
        $this->data = $data;
        
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }
}
