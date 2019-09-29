<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * 微网站
 *
 * Class MobileController
 * @package App\Http\Controllers\Wechat
 */
class MobileController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    /** MobileController constructor. */
    function __construct() { }
    
    /**
     * 微网站首页
     *
     * @return bool|Factory|RedirectResponse|Redirector|View
     */
    public function index() {
        
        return view('wechat.mobile.index');
        
    }
    
    /**
     * 微网站栏目
     *
     * @return Factory|View
     */
    public function column() {
        
        return view('wechat.mobile.column');
        
    }
    
    /**
     * 微网站文章
     *
     * @return Factory|View
     */
    public function article() {
        
        return view('wechat.mobile.article');
        
    }
    
}
