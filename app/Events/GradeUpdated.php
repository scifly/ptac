<?php
namespace App\Events;

use App\Models\Grade;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GradeUpdated {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $grade;

    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param Grade $grade
     */
    public function __construct(Grade $grade) {

        $this->grade = $grade;

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
