<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceSettingRequest;
use App\Models\StudentAttendanceSetting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学生考勤设置
 *
 * Class StudentAttendanceSettingController
 * @package App\Http\Controllers
 */
class StudentAttendanceSettingController extends Controller {
    
    protected $sas;
    
    function __construct(StudentAttendanceSetting $sas) {
    
        $this->middleware(['auth']);
        $this->sas = $sas;
        
    }
    
    /**
     * 学生考勤设置列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->sas->datatable());
        }
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 创建学生考勤设置
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存学生考勤设置
     *
     * @param StudentAttendanceSettingRequest $request
     * @return JsonResponse
     */
    public function store(StudentAttendanceSettingRequest $request) {
        
        return $this->sas->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑学生考勤设置
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $sas = $this->sas->find($id);
        if (!$sas) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['sas' => $sas]);
        
    }
    
    /**
     * 更新学生考勤设置
     *
     * @param StudentAttendanceSettingRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(StudentAttendanceSettingRequest $request, $id) {
        
        $sas = $this->sas->find($id);
        if (!$sas) { return $this->notFound(); }
        
        return $sas->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除学生考勤设置
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $sas = $this->sas->find($id);
        if (!$sas) { return $this->notFound(); }
        
        return $sas->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
