<?php

namespace App\Http\Controllers;

use App\Http\Requests\MajorRequest;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;

class MajorController extends Controller {
    
    protected $major, $subject;
    
    function __construct(Major $major, Subject $subject) {
        
        $this->major = $major;
        $this->subject = $subject;
    
    }
    
    /**
     * 显示专业列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->major->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建专业记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__, [
            'subjects' => $this->subject->subjects(1)
        ]);
        
    }
    
    /**
     * 保存新创建的专业记录
     *
     * @param MajorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MajorRequest $request) {
        
        return $this->major->store($request) ? $this->succeed() : $this->fail();

    }
    
    /**
     * 显示指定的专业记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $major = $this->major->find($id);
        if (!$major) { return $this->notFound(); }
        return $this->output(__METHOD__, ['major' => $major]);
        
    }
    
    /**
     * 显示编辑指定专业记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $major = $this->major->find($id);
        if (!$major) { return $this->notFound(); }
        $majorSubjects = $major->subjects;
        $selectedSubjects = [];
        foreach($majorSubjects as $subject) {
            $selectedSubjects[$subject->id] = $subject->name;
        }
        return $this->output(__METHOD__, [
            'major' => $major,
            'subjects' => $this->subject->subjects(1),
            'selectedSubjects' => $selectedSubjects,
        ]);

    }
    
    /**
     * 更新指定的专业记录
     *
     * @param MajorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MajorRequest $request, $id) {
    
        $major = $this->major->find($id);
        if (!$major) { return $this->notFound(); }
        return $major->modify($request, $id) ? $this->succeed() : $this->fail();
    
    }
    
    /**
     * 删除指定的专业记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $major = $this->major->find($id);
        if (!$major) { return $this->notFound(); }
        return $major->remove($id) ? $this->succeed() : $this->fail();
    
    }
    
}
