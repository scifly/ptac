<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorAttendanceSettingRequest;
use App\Models\EducatorAttendanceSetting;
use Illuminate\Support\Facades\Request;

class EducatorAttendanceSettingController extends Controller
{
    protected $educatorAttendanceSetting;

    function __construct(EducatorAttendanceSetting $educatorAttendanceSetting) {

        $this->educatorAttendanceSetting = $educatorAttendanceSetting;

    }

    /**
     * 显示教职员工考勤设置列表.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Request::get('draw')) {
            return response()->json($this->educatorAttendanceSetting->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * 显示创建教职工考勤设置表单.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);
    }

    /**
     * 保存新创建的教职工考勤设置记录.
     * @param EducatorAttendanceSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EducatorAttendanceSettingRequest $request)
    {
        if ($this->educatorAttendanceSetting->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->educatorAttendanceSetting->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function show(EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }
}
