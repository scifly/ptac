<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use Illuminate\Support\Facades\Request;

class SemesterController extends Controller {
    
    protected $semester;
    
    function __construct(Semester $semester) { $this->semester = $semester; }
    
    /**
     * 显示学期记录列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->semester->datatable());
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 显示创建学期记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的学期记录
     *
     * @param SemesterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SemesterRequest $request) {
        
        return $this->semester->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的学期记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $semester = $this->semester->find($id);
        if (!$semester) { return $this->notFound(); }
        return $this->output(__METHOD__, ['semester' => $semester]);

    }
    
    /**
     * 显示编辑指定学期记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $semester = $this->semester->find($id);
        if (!$semester) { return $this->notFound(); }
        return $this->output(__METHOD__, ['semester' => $semester]);

    }
    
    /**
     * 更新指定的学期记录
     *
     * @param SemesterRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SemesterRequest $request, $id) {

        $semester = $this->semester->find($id);
        if (!$semester) { return $this->notFound(); }
        return $semester->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的学期记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $semester = $this->semester->find($id);
        if (!$semester) { return $this->notFound(); }
        return $semester->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
