<?php
namespace App\Http\Controllers;

use App\Http\Requests\SubjectModuleRequest;
use App\Models\SubjectModule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 科目次分类
 *
 * Class SubjectModuleController
 * @package App\Http\Controllers
 */
class SubjectModuleController extends Controller {
    
    protected $subjectModule;
    
    function __construct(SubjectModule $subjectModule) {
    
        $this->middleware(['auth']);
        $this->subjectModule = $subjectModule;
        
    }
    
    /**
     * 科目次分类列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->subjectModule->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建科目次分类
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存科目次分类
     *
     * @param SubjectModuleRequest $request
     * @return JsonResponse
     */
    public function store(SubjectModuleRequest $request) {
        
        return $this->subjectModule->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑目次分类
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        
        return $this->output(['subjectModules' => $subjectModule]);
        
    }
    
    /**
     * 更新科目次分类
     *
     * @param SubjectModuleRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SubjectModuleRequest $request, $id) {
        
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        
        return $subjectModule->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除科目次分类
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        
        return $subjectModule->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
