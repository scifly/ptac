<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Support\Facades\Request;

class StudentController extends Controller {
    
    protected $student;
    
    function __construct(Student $student) { $this->student = $student; }
    
    /**
     * 显示学生列表
     *
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->student->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建学生记录的表单
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的学生记录
     *
     * @param StudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StudentRequest $request) {
        

        return $this->student->create($request->all()) ? $this->succeed() : $this->fail();
        
    }

    /**
     * 显示指定的学生记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }
        return $this->output(__METHOD__, ['student' => $student]);
        
    }
    
    /**
     * 显示编辑指定学生记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }
        return $this->output(__METHOD__, ['student' => $student]);
        
    }
    
    /**
     * 更新指定的学生记录
     *
     * @param StudentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StudentRequest $request, $id) {
        
        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }

        return $student->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的学生记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }
        return $student->delete() ? $this->succeed() : $this->fail();
        
    }
}
