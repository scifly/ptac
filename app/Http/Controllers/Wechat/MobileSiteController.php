<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MobileSiteController extends Controller {
    
    use WechatTrait;
    
    protected $media, $department, $ws, $wsm, $wsma;
    
    const APP = '微网站';
    
    function __construct(WapSite $ws, WapSiteModule $wsm, WsmArticle $wsma) {
        
        $this->ws = $ws;
        $this->wsm = $wsm;
        $this->wsma = $wsma;
        
    }
    
    /**
     * 微网站首页
     *
     * @return Factory|View
     */
    public function index() {
        
        return Auth::id()
            ? $this->ws->wIndex()
            : $this->getUserid(self::APP);
        
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
