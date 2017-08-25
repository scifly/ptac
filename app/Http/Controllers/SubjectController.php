<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;

class SubjectController extends Controller {
    
    protected $subject;
    
    function __construct(Subject $subject) { $this->subject = $subject; }
    
    /**
     * 显示科目列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->subject->datatable());
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 显示创建新科目的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的科目记录
     *
     * @param SubjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubjectRequest $request) {
        
        if ($this->subject->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->subject->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定科目记录的详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $subject = $this->subject->find($id);
        if (!$subject) { return $this->notFound(); }
        return $this->output(__METHOD__, ['subject' => $subject]);
        
    }
    
    /**
     * 显示编辑指定科目记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $subject = $this->subject->find($id);
        if (!$subject) { return $this->notFound(); }
        $gradeIds = explode(',', $subject['grade_ids']);
        $selectedGrades = [];
        foreach ($gradeIds as $gradeId) {
            $grade = Grade::whereId($gradeId)->first();
            $selectedGrades[$gradeId] = $grade['name'];
        }
        return parent::output(__METHOD__, [
            'subject' => $subject,
            'selectedGrades' => $selectedGrades
        ]);
        
    }
    
    /**
     * 更新指定的科目记录
     *
     * @param SubjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SubjectRequest $request, $id) {
        
        $subject = $this->subject->find($id);
        if (!$subject) { return $this->notFound(); }
        if ($this->subject->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $subject->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的科目记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $subject = $this->subject->find($id);
        if (!$subject) { return $this->notFound(); }
        return $subject->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
