<?php
namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentUpdated {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    
    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param $data
     * @internal param App $app
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
