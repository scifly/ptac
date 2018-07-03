<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class JobResponse
 * @package App\Events
 */
class JobResponse implements ShouldBroadcast {
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $response;
    
    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param $response
     * @internal param App $app
     */
    public function __construct(array $response) {
        
        $this->response = $response;
        
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
    
        return new PrivateChannel(
            'user.' . $this->response['userId']
        );
        
    }
    
}
