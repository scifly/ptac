<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorAttendanceSettingRequest;
use App\Models\EducatorAttendanceSetting;
use Illuminate\Support\Facades\Request;

/**
 * 教职员工考勤设置
 *
 * Class EducatorAttendanceSettingController
 * @package App\Http\Controllers
 */
class EducatorAttendanceSettingController extends Controller {
    
    protected $educatorAttendanceSetting;
    
    function __construct(EducatorAttendanceSetting $educatorAttendanceSetting) {
        
        $this->educatorAttendanceSetting = $educatorAttendanceSetting;
        
    }
    
    /**
     * 教职员工考勤设置列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->educatorAttendanceSetting->datatable());
        }
        return parent::output(__METHOD__);
    }
    
    /**
     * 创建教职工考勤设置
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存教职工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function store(EducatorAttendanceSettingRequest $request) {

        return $this->educatorAttendanceSetting->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 教职员工考勤详情
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorAttendanceSetting $educatorAttendanceSetting
     */
    public function show($id) {
        
        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'educatorAttendanceSetting' => $educatorAttendanceSetting,
        ]);
        
    }


    /**
     * 编辑教职员工考勤设置
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, [
            'educatorAttendanceSetting' => $educatorAttendanceSetting,
        ]);
    }
    
    /**
     * 更新教职员工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function update(EducatorAttendanceSettingRequest $request, $id) {
        
        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) {
            return $this->notFound();
        }

        return $educatorAttendanceSetting->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除教职员工考勤设置
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $educatorAttendanceSetting = $this->educatorAttendanceSetting->find($id);
        if (!$educatorAttendanceSetting) {
            return $this->notFound();
        }
        return $educatorAttendanceSetting->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
