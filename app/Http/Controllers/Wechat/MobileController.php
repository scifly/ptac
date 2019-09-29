<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * 手机微网站
 *
 * Class MobileController
 * @package App\Http\Controllers\Wechat
 */
class MobileController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    /** MobileController constructor. */
    function __construct() { }
    
    /**
     * 首页
     *
     * @return bool|Factory|RedirectResponse|Redirector|View
     */
    public function index() {
        
        return view('wechat.mobile.index');
        
    }
    
    /**
     * 栏目
     *
     * @return Factory|View
     */
    public function column() {
        
        return view('wechat.mobile.column');
        
    }
    
    /**
     * 文章
     *
     * @return Factory|View
     */
    public function article() {
        
        return view('wechat.mobile.article');
        
    }
    
}
