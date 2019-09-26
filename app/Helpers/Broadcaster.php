<?php
namespace App\Helpers;
use Pusher\Pusher;
use Pusher\PusherException;

/**
 * Class Broadcaster
 * @package App\Helpers
 */
class Broadcaster {
    
    protected $pusher;
    
    /**
     * Broadcaster constructor.
     * @throws PusherException
     */
    function __construct() {
        
        $path = 'broadcasting.connections.pusher.';
        $this->pusher = new Pusher(
            config($path . 'key'),
            config($path . 'secret'),
            config($path . 'app_id'),
            [
                'cluster' => config($path . 'options.cluster'),
                'encrypted' => true
            ]
        );
        
    }
    
    /**
     * @param array $response
     * @throws PusherException
     */
    function broadcast(array $response) {
        
        $this->pusher->trigger(
            'channel.' . $response['userId'],
            'broadcast',
            $response
        );
        
    }
    
}