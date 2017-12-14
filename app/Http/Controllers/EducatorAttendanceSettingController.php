<?php
namespace App\Http\Controllers;

use App\Http\Requests\EducatorAttendanceSettingRequest;
use App\Models\EducatorAttendanceSetting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 教职员工考勤设置
 *
 * Class EducatorAttendanceSettingController
 * @package App\Http\Controllers
 */
class EducatorAttendanceSettingController extends Controller {
    
    protected $eas;
    
    function __construct(EducatorAttendanceSetting $eas) {
    
        $this->middleware(['auth']);
        $this->eas = $eas;
        
    }
    
    /**
     * 教职员工考勤设置列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->eas->datatable());
        }
        
        return parent::output(__METHOD__);
    }
    
    /**
     * 创建教职工考勤设置
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存教职工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @return JsonResponse
     */
    public function store(EducatorAttendanceSettingRequest $request) {
        return $this->eas->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 教职员工考勤详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function show($id) {
        
        $eas = $this->eas->find($id);
        if (!$eas) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['eas' => $eas]);
        
    }
    
    /**
     * 编辑教职员工考勤设置
     * @param $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $eas = $this->eas->find($id);
        if (!$eas) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['eas' => $eas]);
    }
    
    /**
     * 更新教职员工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(EducatorAttendanceSettingRequest $request, $id) {
        
        $eas = $this->eas->find($id);
        if (!$eas) {
            return $this->notFound();
        }
        
        return $eas->update($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除教职员工考勤设置
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $eas = $this->eas->find($id);
        if (!$eas) { return $this->notFound(); }
        
        return $eas->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
