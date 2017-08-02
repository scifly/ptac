<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianStudentRequest;
use App\Models\CustodianStudent;
use App\Models\Custodian;
use App\Models\Student;
use Illuminate\Support\Facades\Request;

class CustodianStudentController extends Controller
{
    protected $custodianStudent;

    function __construct(CustodianStudent $custodianStudent, Custodian $custodian, Student $student)
    {
        $this->custodianStudent = $custodianStudent;
        $this->custodian = $custodian;
        $this->student = $student;
    }

    /**
     * 显示监护人列表
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->custodianStudent->datatable());
        }
        return view('custodian_student.index', [
            'js' => 'js/custodian_student/index.js',
            'dialog' => true,
            'datatable' => true,
            'form'=>true,
        ]);
    }

    /**
     * 添加监护人和学生关系的页面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('custodian_student.create',[
            'js' => 'js/custodian_student/create.js',
            'form' => true
        ]);
    }

    /**
     * 创建监护人和学生之间的关系
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustodianStudentRequest $request)
    {
        $data = $request->except('_token');
        if ($this->custodianStudent->create($data)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        }else{
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = self::添加失败;
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function show(CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $custodianStudent = $this->custodianStudent->findOrFail($id)->toArray();
        $custodian_id = $custodianStudent['custodian_id'];

        $custodian = $this->custodian->where('id',$custodian_id)->pluck('user_id');
        $custodianStudent['custodian_id'] = $custodian[0];
        $student_id = $custodianStudent['student_id'];
        $student = $this->student->where('id',$student_id)->pluck('user_id');
        $custodianStudent['student_id'] = $student[0];

        return view('custodian_student.edit', [
            'js' => 'js/custodian_student/edit.js',
            'custodianStudent' => $custodianStudent,
            'form' => true
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustodianStudent $custodianStudent)
    {
        //
    }
}
