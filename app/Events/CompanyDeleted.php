<?php
namespace App\Events;

use App\Models\Company;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyDeleted {
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $company;
    
    /**
     * Create a new event instance.
     *
     * @internal param School $school
     * @param Company $company
     */
    public function __construct(Company $company) {
        
        $this->company = $company;
        
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
