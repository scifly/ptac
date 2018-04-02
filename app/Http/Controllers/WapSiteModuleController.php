<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\WapSiteModuleRequest;
use App\Models\Media;
use App\Models\WapSiteModule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微网站栏目
 *
 * Class WapSiteModuleController
 * @package App\Http\Controllers
 */
class WapSiteModuleController extends Controller {
    
    protected $wsm;
    
    public function __construct(WapSiteModule $wsm) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->wsm = $wsm;
        
    }
    
    /**
     * 微网站栏目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->wsm->datatable()
            );
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
        
        return $this->result(
            $this->wsm->store($request)
        );
        
    }
    
    /**
     * 微网站栏目详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $wsm = $this->wsm->find($id);
        abort_if(!$wsm, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'wsm'   => $wsm,
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
        
        $wsm = $this->wsm->find($id);
        abort_if(!$wsm, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'wapSiteModule' => $wsm,
            'media'         => Media::find($wsm->media_id),
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
        
        $wsm = $this->wsm->find($id);
        abort_if(!$wsm, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $wsm->modify($request, $id)
        );
        
    }
    
    /**
     * 删除微网站栏目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $wsm = $this->wsm->find($id);
        abort_if(!$wsm, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $wsm->delete()
        );
        
    }
    
}
