<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

/**
 * 企业微信应用入口
 *
 * Class WechatController
 * @package App\Http\Controllers\Wechat
 */
class WechatController extends Controller {
    
    static $category = 2;
    
    protected $module, $user;
    
    /**
     * WechatController constructor.
     * @param Module $module
     * @param User $user
     */
    function __construct(Module $module, User $user) {
    
        $this->middleware(['corp.auth', 'corp.role']);
        $this->module = $module;
        $this->user = $user;
        
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
    
        return view('wechat.roles', [
            'appId' => session('appId') ? '/' . session('appId') : ''
        ]);
    
    }
    
    /**
     * 公众号用户注册 & 绑定
     *
     * @param $appId - 公众号应用id
     * @return Factory|JsonResponse|View
     * @throws Throwable
     */
    function signup($appId) {
    
        return $this->user->signup($appId);
    
    }
    
}
