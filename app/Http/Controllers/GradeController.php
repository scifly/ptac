<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Educator;
use App\Models\Grade;
use Illuminate\Support\Facades\Request;

class GradeController extends Controller {
    
    protected $grade, $educator;
    
    function __construct(Grade $grade, Educator $educator) {
        
        $this->grade = $grade;
        $this->educator = $educator;
    }
    
    /**
     * 显示年级列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->grade->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建年级记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的年级记录
     *
     * @param GradeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GradeRequest $request) {
        
        if ($this->grade->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->grade->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的年级记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $grade = $this->grade->find($id);
        if (!$grade) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'grade' => $grade,
            'educators' => $this->educator->educators($grade->educator_ids)
        ]);
        
    }
    
    /**
     * 显示编辑年级记录的表单
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $grade = $this->grade->find($id);
        if (!$grade) { return $this->notFound(); }
        $gradeIds = explode(",", $grade->educator_ids);

        return $this->output(__METHOD__, [
            'grade' => $grade,
            'selectedEducators' => $this->educator->educators($gradeIds)
        ]);
        
    }
    
    /**
     * 更新指定的年级记录
     *
     * @param GradeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GradeRequest $request, $id) {
        
        $grade = $this->grade->find($id);
        if (!$grade) { return $this->notFound(); }
        if ($this->grade->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $grade->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除指定的年级记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $grade = $this->grade->find($id);
        if (!$grade) { return $this->notFound(); }
        return $grade->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
