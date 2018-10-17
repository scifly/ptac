<?php
namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 应用模块
 *
 * Class WapSiteModuleController
 * @package App\Http\Controllers
 */
class ModuleController extends Controller {
    
    protected $module;
    
    /**
     * WapSiteModuleController constructor.
     * @param Module $module
     */
    public function __construct(Module $module) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->module = $module;
        $this->approve($module);
        
    }
    
    /**
     * 应用模块列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->module->index())
            : $this->output();
    }
    
    /**
     * 创建应用模块
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->module->upload()
            : $this->output();
        
    }
    
    /**
     * 保存应用模块
     *
     * @param ModuleRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(ModuleRequest $request) {
        
        return $this->result(
            $this->module->store($request->all())
        );
        
    }
    
    /**
     * 编辑应用模块
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->module->upload()
            : $this->output([
                'module' => $this->module->find($id)
            ]);
        
    }
    
    /**
     * 更新应用模块
     *
     * @param ModuleRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(ModuleRequest $request, $id) {
        
        return $this->result(
            $this->module->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除应用模块
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->module->remove($id)
        );
        
    }
    
}
