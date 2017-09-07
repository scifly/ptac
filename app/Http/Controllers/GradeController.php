<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Educator;
use App\Models\Grade;
use Illuminate\Support\Facades\Request;

/**
 * 年级
 *
 * Class GradeController
 * @package App\Http\Controllers
 */
class GradeController extends Controller {
    
    protected $grade, $educator;
    
    function __construct(Grade $grade, Educator $educator) {
        
        $this->grade = $grade;
        $this->educator = $educator;
    }
    
    /**
     * 年级列表
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
     * 创建年级
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存年级
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
     * 年级详情
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
     * 编辑年级
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
     * 更新年级
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
     * 删除年级
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
