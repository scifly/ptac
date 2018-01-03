<?php
namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MessageCreated implements ShouldBroadcast {

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
        return new PrivateChannel('student.'. Auth::id());
    }
    
    public function broadcastWith()
    {
        return ['user_id' => '132'];
    }
}
