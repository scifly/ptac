<?php
namespace App\Http\Controllers;

use App\Http\Requests\WapSiteModuleRequest;
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
    
    /**
     * WapSiteModuleController constructor.
     * @param WapSiteModule $wsm
     */
    public function __construct(WapSiteModule $wsm) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->wsm = $wsm;
        $this->approve($wsm);
        
    }
    
    /**
     * 微网站栏目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->wsm->index())
            : $this->output();
    }
    
    /**
     * 创建微网站栏目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->wsm->import()
            : $this->output();
        
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
            $this->wsm->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑微网站栏目
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->wsm->import()
            : $this->output([
                'wsm' => $this->wsm->find($id)
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
        
        return $this->result(
            $this->wsm->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除微网站栏目
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->wsm->remove($id)
        );
        
    }
    
}
