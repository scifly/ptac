<?php

namespace App\Http\Controllers;

use App\Models\Action;

class ActionController extends Controller {
    
    protected $action;
    
    function __construct(Action $action) {
        
        $this->action = $action;
        
    }
    
    public function index() {
        
        $this->action->scan();
        
    }
    
}
