<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendanceSetting;
use Illuminate\Support\Facades\Request;

class StudentAttendanceSettingController extends Controller
{
    protected $studentAttendanceSetting;

    function __construct(StudentAttendanceSetting $studentAttendanceSetting) {

        $this->studentAttendanceSetting = $studentAttendanceSetting;

    }

    /**
     * 显示学生考勤设置列表.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->studentAttendanceSetting->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * 显示创建学生考勤设置的表单.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StudentAttendanceSetting  $studentAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function show(StudentAttendanceSetting $studentAttendanceSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StudentAttendanceSetting  $studentAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(StudentAttendanceSetting $studentAttendanceSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentAttendanceSetting  $studentAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentAttendanceSetting $studentAttendanceSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StudentAttendanceSetting  $studentAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentAttendanceSetting $studentAttendanceSetting)
    {
        //
    }
}
