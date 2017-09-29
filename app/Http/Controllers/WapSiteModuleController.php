<?php
namespace App\Http\Controllers;

use App\Http\Requests\WapSiteModuleRequest;
use App\Models\Media;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Illuminate\Support\Facades\Request;

/**
 * 微网站栏目
 *
 * Class WapSiteModuleController
 * @package App\Http\Controllers
 */
class WapSiteModuleController extends Controller {
    
    protected $wapSiteModule;
    protected $media;
    
    public function __construct(WapSiteModule $wapSiteModule, Media $media) {
        
        $this->wapSiteModule = $wapSiteModule;
        $this->media = $media;
        
    }
    
    /**
     * 微网站栏目列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->wapSiteModule->datatable());
        }
        
        return $this->output(__METHOD__);
    }
    
    /**
     * 创建微网站栏目
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存微网站栏目
     *
     * @param WapSiteModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WapSiteModuleRequest $request) {
        
        return $this->wapSiteModule->store($request) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 微网站栏目详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $module = $this->wapSiteModule->find($id);
        if (!$module) {
            return parent::notFound();
        }
        
        return parent::output(__METHOD__, [
            '$module' => $module,
            'media'   => $this->media->find($module->media_id),
        ]);
        
    }
    
    /**
     * 编辑微网站栏目
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $wapSiteModule = $this->wapSiteModule->find($id);
        if (!$wapSiteModule) {
            return parent::notFound();
        }
        return parent::output(__METHOD__, [
            'wapSiteModule' => $wapSiteModule,
            'media'         => $this->media->find($wapSiteModule->media_id),
        ]);
        
    }
    
    /**
     * 更新微网站栏目
     *
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WapSiteModuleRequest $request, $id) {
        
        return $this->wapSiteModule->modify($request, $id) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除微网站栏目
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $wapSiteModule = $this->wapSiteModule->find($id);
        if (!$wapSiteModule) {
            return parent::notFound();
        }
        
        return $wapSiteModule->delete() ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 微网站栏目首页
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapSiteModuleHome($id) {
        
        $articles = WapSiteModule::whereId($id)->get();
        dd($articles);
        return view('frontend.wap_site.module', [
            'articles' => $articles,
            'ws'       => true,
        ]);
        
    }
}
