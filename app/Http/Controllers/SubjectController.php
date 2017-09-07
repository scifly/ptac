<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;

/**
 * 科目
 *
 * Class SubjectController
 * @package App\Http\Controllers
 */
class SubjectController extends Controller {
    
    protected $subject, $major, $grade;
    
    function __construct(Subject $subject, Major $major, Grade $grade) {
        
        $this->subject = $subject;
        $this->major = $major;
        $this->grade = $grade;
    
    }
    
    /**
     * 科目列表
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
     * 创建科目
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        return parent::output(__METHOD__, [
            'majors' => $this->major->majors(1),
//            'grades' => $this->grade->grades(1)
        ]);
        
    }
    
    /**
     * 保存科目
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
     * 科目详情
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
     * 编辑科目
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
        $subjectMajors = $this->subject->majors;
        $selectedMajors = [];
        foreach ($subjectMajors as $major) {
            $selectedMajors[$major->id] = $major->name;
        }
        return parent::output(__METHOD__, [
            'subject' => $subject,
            'grades' => $this->grade->grades(1),
            'selectedGrades' => $selectedGrades,
            'majors' => $this->major->majors(1),
            'selectedMajors' => $selectedMajors
        ]);
        
    }
    
    /**
     * 更新科目
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
     * 删除科目
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
