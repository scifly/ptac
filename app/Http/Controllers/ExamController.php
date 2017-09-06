<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\Squad;
use Illuminate\Support\Facades\Request;

class ExamController extends Controller {
    
    protected $exam;
    protected $squad;

    function __construct(Exam $exam, Squad $squad) {

        $this->exam = $exam;
        $this->squad = $squad;

    }
    
    /**
     * 显示考试列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->exam->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建考试记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的考试记录
     *
     * @param ExamRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExamRequest $request) {
        
        return $this->exam->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的考试记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $exam = $this->exam->find($id);
        if (!$exam) { return $this->notFound(); }
        
        return $this->output(__METHOD__, [
            'exam' => $exam,
            'classes' => $this->exam->classes($exam->class_ids),
            'subjects' => $this->exam->subjects(),
        ]);
        
    }
    
    /**
     * 显示编辑指定考试记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $exam = $this->exam->find($id);
        if (!$exam) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'exam' => $exam,
            'selectedClasses' => $this->exam->classes($exam->class_ids),
            'selectedSubjects' => $this->exam->subjects($exam->subject_ids),
        ]);
    }
    
    /**
     * 更新指定的考试记录
     *
     * @param ExamRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExamRequest $request, $id) {
        
        $exam = $this->exam->find($id);
        if (!$exam) { return $this->notFound(); }
        return $exam->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的考试记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $exam = $this->exam->find($id);
        if (!$exam) { return $this->notFound(); }
        return $exam->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
