<?php
namespace App\Events;

use App\Models\Squad;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassDeleted {
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $class;
    
    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param Squad $class
     */
    public function __construct(Squad $class) {
        $this->class = $class;
        
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
