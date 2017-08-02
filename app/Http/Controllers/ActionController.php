<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Support\Facades\Request;

class ActionController extends Controller {
    
    protected $action;
    
    function __construct(Action $action) {
        
        $this->action = $action;
        
    }
    
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->action->datatable());
        }
        $this->action->scan();
        return view('action.index', [
            'js' => 'js/action/index.js',
            'datatable' => true
        ]);
        
    }
    
}
