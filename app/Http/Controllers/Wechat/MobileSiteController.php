<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

class MobileSiteController extends Controller {
    
    use WechatTrait;
    
    protected $media, $department, $ws, $wsm, $wsma;
    
    function __construct(WapSite $ws, WapSiteModule $wsm, WsmArticle $wsma) {

        $this->middleware('wechat');
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
