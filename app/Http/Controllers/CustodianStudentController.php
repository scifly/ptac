<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianStudentRequest;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Student;
use Illuminate\Support\Facades\Request;

class CustodianStudentController extends Controller {
    protected $custodianStudent;
    
    function __construct(CustodianStudent $custodianStudent, Custodian $custodian, Student $student) {
        $this->custodianStudent = $custodianStudent;
        $this->custodian = $custodian;
        $this->student = $student;
    }
    
    /**
     * 显示监护人列表
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->custodianStudent->datatable());
        }
        return parent::output(__METHOD__);
    }
    
    /**
     * 添加监护人和学生关系的页面
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return parent::output(__METHOD__);
    }
    
    /**
     * 创建监护人和学生之间的关系
     * @param CustodianStudentRequest $request
     * @return \Illuminate\Http\Response
     * @internal param $CustodianStudentRequest
     */
    public function store(CustodianStudentRequest $request) {

        $data = $request->except('_token');
        if ($this->custodianStudent->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->custodianStudent->create($data) ? $this->succeed() : $this->fail();


    }
    
    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param CustodianStudent $custodianStudent
     */
    public function show($id) {
        return view('custodian_student.show', [
            'custodianStudent' => $this->custodianStudent->findOrFail($id)
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param CustodianStudent $custodianStudent
     */
    public function edit($id) {

        $custodianStudent = $this->custodianStudent->find($id);
        if (!$custodianStudent) { return $this->notFound(); }
        return $this->output(__METHOD__, ['custodianStudent' => $custodianStudent]);
        
    }
    
    /**
     * 更改监护人和学生之间的关系
     * @param CustodianStudentRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param CustodianStudent $custodianStudent
     */
    public function update(CustodianStudentRequest $request, $id) {
        $custodianStudent = $this->custodianStudent->find($id);
        if (!$custodianStudent) { return $this->notFound(); }
        if ($this->custodianStudent->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $custodianStudent->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除监护人和学生之间的关系
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param CustodianStudent $custodianStudent
     */
    public function destroy($id) {
        $custodianStudent = $this->custodianStudent->find($id);
        if (!$custodianStudent) { return $this->notFound(); }
        return $custodianStudent->delete() ? $this->succeed() : $this->fail();
    }
}
