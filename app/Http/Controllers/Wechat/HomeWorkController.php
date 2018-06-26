<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;

class HomeWorkController extends Controller {
    
    protected $hw;
    
    function __construct() {
        
        $this->middleware('wechat');
        
    }
    
    public function index() {
        
        return $this->hw->wIndex();
        
    }
    
}
