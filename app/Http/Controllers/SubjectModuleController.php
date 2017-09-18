<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectModuleRequest;
use App\Models\SubjectModule;
use Illuminate\Support\Facades\Request;

/**
 * 科目次分类
 *
 * Class SubjectModuleController
 * @package App\Http\Controllers
 */
class SubjectModuleController extends Controller {
    
    protected $subjectModule;
    
    function __construct(SubjectModule $subjectModule) {
        
        $this->subjectModule = $subjectModule;
        
    }
    
    /**
     * 科目次分类列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->subjectModule->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建科目次分类
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存科目次分类
     *
     * @param SubjectModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubjectModuleRequest $request) {

        return $this->subjectModule->create($request->all()) ? $this->succeed() : $this->fail();

    }
    
    /**
     * 科目次分类详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        return $this->output(__METHOD__, ['subjectModule' => $subjectModule]);
        
    }
    
    /**
     * 编辑目次分类
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        return $this->output(__METHOD__, ['subjectModules' => $subjectModule]);
        
    }
    
    /**
     * 更新科目次分类
     *
     * @param SubjectModuleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        return $subjectModule->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
