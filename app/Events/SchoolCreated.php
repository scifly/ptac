<?php
namespace App\Events;

use App\Models\School;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchoolCreated {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $school;

    /**
     * Create a new event instance.
     *
     * @param School $school
     */
    public function __construct(School $school) {

        $this->school = $school;

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
