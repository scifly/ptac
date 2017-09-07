<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectModuleRequest;
use App\Models\SubjectModule;
use Illuminate\Support\Facades\Request;

class SubjectModuleController extends Controller {
    
    protected $subjectModule;
    
    function __construct(SubjectModule $subjectModule) {
        
        $this->subjectModule = $subjectModule;
        
    }
    
    /**
     * 显示科目次分类列表
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
     * 显示创建科目次分类记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的科目次分类记录
     *
     * @param SubjectModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubjectModuleRequest $request) {
        
        if ($this->subjectModule->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->subjectModule->create($request->all()) ? $this->succeed() : $this->fail();

    }
    
    /**
     * 显示指定的科目次分类记录详情
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
     * 显示编辑指定科目次分类记录的表单
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
     * 更新指定的科目次分类记录
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
     * 删除指定的科目次分类记录
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
