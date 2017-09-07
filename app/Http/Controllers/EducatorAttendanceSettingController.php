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

        return $this->educatorAttendanceSetting->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorAttendanceSetting $educatorAttendanceSetting
     */
    public function show($id)
    {
        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educatorAttendanceSetting' => $educatorAttendanceSetting,
        ]);
    }

    /**
     * 显示编辑指定教职员工考勤设置记录的表单.

     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $educatorAttendanceSetting= $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educatorAttendanceSetting' => $educatorAttendanceSetting,
        ]);
    }

    /**
     * 更新指定的考勤设置记录.
     * @param EducatorAttendanceSettingRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EducatorAttendanceSettingRequest $request,$id)
    {

        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) { return $this->notFound(); }

        return $educatorAttendanceSetting->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除指定的教职员工考勤设置记录.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) { return $this->notFound(); }
        return $educatorAttendanceSetting->delete() ? $this->succeed() : $this->fail();
    }
}
