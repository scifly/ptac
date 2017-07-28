<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Support\Facades\Request;

class StudentController extends Controller
{
    protected $student;

    function __construct(Student $student){ $this->student = $student; }

    /**
     * 显示学生列表
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->student->datatable());
        }
        return view('student.index', [
            'js' => 'js/student/index.js',
            'dialog' => true,
            'datatable' => true,
            'form'=>true,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('student.create',[
            'js' => 'js/student/create.js',
            'form' => true
        ]);
    }

    /**
     * 添加学生
     * @param \Illuminate\Http\Request|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentRequest $request)
    {
        if ($this->student->create($request->except('_token'))) {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_OK, 'message' => self::MSG_CREATE_OK,
            ]);
        }
        return response()->json([
            'statusCode' => 500, 'message' => '添加失败'
        ]);
    }

    /**
     * 学生详情页面.
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id){

        $student = $this->student->findOrFail($id);
        return view('student.show', ['student' => $student]);
    }

    /**
     *  编辑学生页面
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id){

        return view('student.edit', [
            'js' => 'js/student/edit.js',
            'student' => $this->student->findOrFail($id),
            'form' => true
        ]);
    }

    /**
     * 更新指定学生的信息
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Company $company
     */
    public function update(StudentRequest $request, $id){

        if ($this->student->findOrFail($id)->update($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '更新失败';
        }
        return response()->json($this->result);


    }

    /**
     * 删除学生
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        if ($this->student->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);

    }
}
