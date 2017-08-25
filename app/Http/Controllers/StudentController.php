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
        return parent::output(__METHOD__);
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::output(__METHOD__);
    }

    /**
     * 添加学生
     * @param StudentRequest $request
     * @return \Illuminate\Http\Response
     * @internal param $StudentRequest
     */
    public function store(StudentRequest $request)
    {
        $data = $request->except('_token');
        if ($this->student->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->student->create($data) ? $this->succeed() : $this->fail();

    }

    /**
     * 学生详情页面.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Student $student
     */
    public function show($id){
        $students = $this->student->whereId($id)
            ->first([
                'user_id','class_id',
                'student_number',
                'card_number',
                'oncampus',
                'birthday',
                'remark',
                'enabled'
            ]);
        $students->user_id = $students->user->realname;
        $students->class_id = $students->squad->name;
        $students->oncampus = $students->oncampus==1 ? '是' : '否' ;
        $students->enabled = $students->enabled==1 ? '已启用' : '已禁用' ;
        if (!$students) { return $this->notFound(); }
        return $this->output(__METHOD__, ['students' => $students]);
    }

    /**
     *  编辑学生页面
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Student $student
     */
    public function edit($id){

        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }
        return $this->output(__METHOD__, ['student' => $student]);
    }

    /**
     * 更新指定学生的信息
     * @param StudentRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function update(StudentRequest $request, $id){
        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }
        if ($this->student->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $student->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除学生
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Student $student
     */
    public function destroy($id){

        $student = $this->student->find($id);
        if (!$student) { return $this->notFound(); }
        return $student->delete() ? $this->succeed() : $this->fail();

    }
}
