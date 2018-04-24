<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeWorkController extends Controller {
    
    use WechatTrait;
    
    const APP = '成绩中心';
    
    protected $hw;
    
    function __construct() { }
    
    public function index() {
    
        return Auth::id()
            ? $this->hw->wIndex()
            : $this->getUserId(self::APP);
        
    }
    
}
