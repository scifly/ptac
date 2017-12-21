<?php
namespace App\Policies;

class Route {

    public $uri;

    function __construct(string $uri) {
        
        $this->uri = $uri;
        
    }

}