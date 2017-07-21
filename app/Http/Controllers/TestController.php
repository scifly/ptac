<?php

namespace App\Http\Controllers;

use App\Services\Test;

class TestController extends Controller {
    protected $test;
    
    //
    
    function __construct(Test $test) {
        $this->test = $test;
    }
    
    function display() {
        
        return $this->test->respond(['a' => 'whatthefuck']);
        
    }
    
}
