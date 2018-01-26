<?php
namespace App\Http\Controllers;

use App\Http\Requests\WapSiteModuleRequest;
use App\Models\Media;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * 微网站栏目
 *
 * Class WapSiteModuleController
 * @package App\Http\Controllers
 */
class WapSiteModuleController extends Controller {
    
    public function __construct(WapSiteModule $wapSiteModule, Media $media) {
        
        $this->middleware(['auth']);
        
    }
    
    /**
     * 微网站栏目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(WapSiteModule::datatable());
        }
        
        return $this->output();
    }
    
    /**
     * 创建微网站栏目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存微网站栏目
     *
     * @param WapSiteModuleRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(WapSiteModuleRequest $request) {
        
        return $this->result(WapSiteModule::store($request));
    
    }
    
    /**
     * 微网站栏目详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $wsm = WapSiteModule::find($id);
        if (!$wsm) { return $this->notFound(); }
        
        return $this->output([
            'wsm' => $wsm,
            'media' => Media::find($wsm->media_id),
        ]);
        
    }
    
    /**
     * 编辑微网站栏目
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $wsm = WapSiteModule::find($id);
        if (!$wsm) { return $this->notFound(); }
        
        return $this->output([
            'wsm' => $wsm,
            'media' => Media::find($wsm->media_id),
        ]);
        
    }
    
    /**
     * 更新微网站栏目
     *
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(WapSiteModuleRequest $request, $id) {
        
        $wsm = WapSiteModule::find($id);
        if (!$wsm) { return $this->notFound(); }
        
        return $this->result($wsm->modify($request, $id));
        
    }
    
    /**
     * 删除微网站栏目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $wsm = WapSiteModule::find($id);
        if (!$wsm) { return $this->notFound(); }
        
        return $this->result($wsm->delete());
        
    }

    
}
