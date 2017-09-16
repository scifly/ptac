<?php

namespace App\Events;

use App\Models\Corp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CorpUpdated {
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $corp;
    
    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param Corp $corp
     * @internal param Company $company
     */
    public function __construct(Corp $corp) {
        
        $this->corp = $corp;
        
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
