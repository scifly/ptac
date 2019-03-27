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
 * Class MobileSiteController
 * @package App\Http\Controllers\Wechat
 */
class MobileSiteController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    /** MobileSiteController constructor. */
    function __construct() { }
    
    /**
     * 微网站首页
     *
     * @return bool|Factory|RedirectResponse|Redirector|View
     */
    public function index() {
        
        return view('wechat.mobile_site.index');
        
    }
    
    /**
     * 微网站栏目首页
     *
     * @return Factory|View
     */
    public function module() {
        
        return view('wechat.mobile_site.module');
        
    }
    
    /**
     * 微网站文章详情
     *
     * @return Factory|View
     */
    public function article() {
        
        return view('wechat.mobile_site.article');
        
    }
    
}
