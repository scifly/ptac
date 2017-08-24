<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamTypeRequest;
use App\Models\ExamType;
use Illuminate\Support\Facades\Request;


class ExamTypeController extends Controller {
    protected $examType;
    
    function __construct(ExamType $examType) {
        
        $this->examType = $examType;
        
    }
    
    /**
     * 显示考试类型列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->examType->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建考试类型记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的考试类型记录
     *
     * @param ExamTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExamTypeRequest $request) {
        
        return $this->examType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的考试类型记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['examType' => $examType]);
        
    }
    
    /**
     * 显示编辑指定考试类型记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['examType' => $examType]);
    
    }
    
    /**
     * 更新指定的考试类型记录
     *
     * @param ExamTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExamTypeRequest $request, $id) {
    
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        return $examType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的考试类型记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        return $examType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
