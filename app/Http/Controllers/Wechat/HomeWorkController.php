<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class HomeWorkController extends Controller {
    
    use WechatTrait;
    
    protected $hw;
    
    function __construct() {
        
        $this->middleware('wechat');
        
    }
    
    public function index() {
    
        return $this->hw->wIndex();
        
    }
    
}
