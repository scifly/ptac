<?php
namespace App\Policies;
/**
 * Class Route
 * @package App\Policies
 */
class Route {

    public $uri;
    
    /**
     * Route constructor.
     * @param string $uri
     */
    function __construct(string $uri) {
        
        $this->uri = $uri;
        
    }

}