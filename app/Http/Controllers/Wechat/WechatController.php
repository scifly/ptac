<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * 企业微信应用入口
 *
 * Class WechatController
 * @package App\Http\Controllers\Wechat
 */
class WechatController extends Controller {
    
    static $category = 2;
    
    protected $module;
    
    /**
     * WechatController constructor.
     * @param Module $module
     */
    function __construct(Module $module) {
    
        $this->middleware(['corp.auth', 'corp.role']);
        $this->module = $module;
        
    }
    
    /**
     * 企业应用首页
     *
     * @return Factory|View
     */
    function index() {
        
        return $this->module->wIndex();

    }
    
    /**
     * 选择学校
     *
     * @return Factory|View
     */
    function schools() {
        
        return $this->module->schools();
        
    }
    
    /**
     * 选择角色
     *
     * @return Factory|View
     */
    function roles() {
    
        return view('wechat.roles');
    
    }
    
}
