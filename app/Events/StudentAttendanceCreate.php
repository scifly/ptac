<?php
namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentAttendanceCreate {

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
    
}
