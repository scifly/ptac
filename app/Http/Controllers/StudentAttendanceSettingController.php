<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceSettingRequest;
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
     * 保存新创建的学生考勤设置记录.
     * @param StudentAttendanceSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StudentAttendanceSettingRequest $request)
    {
        dd($request->all());
        return $this->studentAttendanceSetting->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 显示编辑指定学生考勤设置记录的详情.
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $studentAttendanceSetting = $this->studentAttendanceSetting->find($id);
        if (!$studentAttendanceSetting) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'studentAttendanceSetting' => $studentAttendanceSetting,
        ]);

    }

    /**
     * 显示编辑指定学生考勤设置记录的表单.
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $studentAttendanceSetting = $this->studentAttendanceSetting->find($id);
        if (!$studentAttendanceSetting) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'studentAttendanceSetting' => $studentAttendanceSetting,
        ]);
    }

    /**
     * 更新指定的学生考勤设置记录.
     * @param StudentAttendanceSettingRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StudentAttendanceSettingRequest $request, $id)
    {
        $studentAttendanceSetting = $this->studentAttendanceSetting->find($id);
        if (!$studentAttendanceSetting) {
            return $this->notFound();
        }
        if ($this->studentAttendanceSetting->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $studentAttendanceSetting->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除指定的学生考勤设置记录.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $studentAttendanceSetting = $this->studentAttendanceSetting->find($id);
        if (!$studentAttendanceSetting) { return $this->notFound(); }
        return $studentAttendanceSetting->delete() ? $this->succeed() : $this->fail();

    }
}
