<?php

namespace App\Http\Controllers;

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
        return view('student.index', ['js' => 'js/student/index.js']);
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
    public function store()
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('student.edit', ['js' => 'js/student/edit.js']);
    }

    /**
     * 更新指定学生的信息
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Company $company
     */
    public function update(Request $request, Student $student)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {

    }
}
