<?php
namespace App\Events;

use App\Models\App;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppUpdated {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $app;
    
    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param App $app
     */
    public function __construct(App $app) {

        $this->app = $app;

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
