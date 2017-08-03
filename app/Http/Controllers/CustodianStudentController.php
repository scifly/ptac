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
        $result = $this->custodianStudent
            ->where('custodian_id',$data['custodian_id'])
            ->where('student_id',$data['student_id'])
            ->get()
            ->toArray();
        if(!empty($result))
        {
            $this->result['statusCode'] = self::MSG_BAD_REQUEST;
            $this->result['message'] = '该条数据已经存在!';
        }else{
            if ($this->custodianStudent->create($data)) {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;
            }else{
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = self::添加失败;
            }
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('custodian_student.show',[
            'custodianStudent' =>$this->custodianStudent->findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $custodianStudent = $this->custodianStudent->findOrFail($id);

        return view('custodian_student.edit', [
            'js' => 'js/custodian_student/edit.js',
            'custodianStudent' => $custodianStudent,
            'form' => true
        ]);

    }

    /**
     * 更改监护人和学生之间的关系
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function update(CustodianStudentRequest $request, $id)
    {
        $data = $request->all();
        $custodianStudent = $this->custodianStudent->findOrFail($id);
        $result = $this->custodianStudent
            ->where('custodian_id',$data['custodian_id'])
            ->where('student_id',$data['student_id'])
            ->first();

        if(!empty($result) && ($result->id !=$id))
        {
            $this->result['statusCode'] = self::MSG_BAD_REQUEST;
            $this->result['message'] = '该条数据已经存在!';
        }else{
            if($custodianStudent->update($data))
            {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '更新失败';
            }
        }

        return response()->json($this->result);
    }

    /**
     * 删除监护人和学生之间的关系
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->custodianStudent->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }
}
