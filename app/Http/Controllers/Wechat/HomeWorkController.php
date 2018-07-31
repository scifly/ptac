<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;

/**
 * Class HomeWorkController
 * @package App\Http\Controllers\Wechat
 */
class HomeWorkController extends Controller {
    
    protected $hw;
    
    function __construct() {
        
        $this->middleware('wechat');
        
    }
    
    public function index() {
        
        // return $this->hw->wIndex();
        
    }
    
}
