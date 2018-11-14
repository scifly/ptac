<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
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
    
    protected $ws, $wsm, $wsma;
    
    /**
     * MobileSiteController constructor.
     * @param WapSite $ws
     * @param WapSiteModule $wsm
     * @param WsmArticle $wsma
     */
    function __construct(WapSite $ws, WapSiteModule $wsm, WsmArticle $wsma) {
        
        $this->middleware(['corp.auth', 'corp.role']);
        $this->ws = $ws;
        $this->wsm = $wsm;
        $this->wsma = $wsma;
        
    }
    
    /**
     * 微网站首页
     *
     * @return bool|Factory|RedirectResponse|Redirector|View
     */
    public function index() {
        
        return $this->ws->wIndex();
        
    }
    
    /**
     * 微网站栏目首页
     *
     * @return Factory|View
     */
    public function module() {
        
        return $this->wsm->wIndex();
        
    }
    
    /**
     * 微网站文章详情
     *
     * @return Factory|View
     */
    public function article() {
        
        return $this->wsma->wIndex();
        
    }
    
}
