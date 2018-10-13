<?php
namespace App\Helpers;
use Pusher\Pusher;

/**
 * Class Broadcaster
 * @package App\Helpers
 */
class Broadcaster {
    
    protected $pusher;
    
    /**
     * Broadcaster constructor.
     * @throws \Pusher\PusherException
     */
    function __construct() {
        
        $configPath = 'broadcasting.connections.pusher.';
        $this->pusher = new Pusher(
            config($configPath . 'key'),
            config($configPath . 'secret'),
            config($configPath . 'app_id'),
            [
                'cluster' => config($configPath . 'options.cluster'),
                'encrypted' => true
            ]
        );
        
    }
    
    /**
     * @param array $response
     * @throws \Pusher\PusherException
     */
    function broadcast(array $response) {
        
        $this->pusher->trigger(
            'channel.' . $response['userId'],
            'broadcast',
            $response
        );
        
    }
    
}