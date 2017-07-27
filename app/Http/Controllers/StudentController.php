<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Support\Facades\Request;

class StudentController extends Controller
{
    protected $student;

    protected $message;

    function __construct(Student $student)
    {
        $this->student = $student;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
    }

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
            'dialog' => true
        ]);
    }

    /**

     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('student.create',['js' => 'js/student/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentRequest $request)
    {
        $data = $request->except('_token');

        if($data !=null){
            $res = Student::create($data);

            if ($res) {
                $this->message['statusCode'] = 200;
                $this->message['message'] = 'nailed it!';
            } else {
                $this->message['statusCode'] = 202;
                $this->message['message'] = 'add filed';
            }
            return response()->json($this->message);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Student::whereId($id)->first();

        return view('student.show', ['student' => $student]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::whereId($id)->first();
        return view('student.edit', [
            'js' => 'js/student/edit.js',
            'student' => $student
        ]);
    }

    /**
     * 更新指定学生的信息
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Company $company
     */
    public function update(StudentRequest $request, $id)
    {
        $student = Student::find($id);
        $student->user_id = $request->get('user_id');
        $student->class_id = $request->get('class_id');
        $student->student_number = $request->get('student_number');
        $student->card_number = $request->get('card_number');
        $student->oncampus = $request->get('oncampus');
        $student->birthday = $request->get('birthday');
        $student->remark = $request->get('remark');

        if ($student->save()) {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
        } else {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        }
        return response()->json($this->message);


    }

    /**
     * 删除学生
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Student::destroy($id);
        if ($res) {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
        }else{
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        }
        return response()->json($this->message);

    }
}
