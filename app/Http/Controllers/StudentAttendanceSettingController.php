<?php
namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendanceSettingRequest;
use App\Models\StudentAttendanceSetting;
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
    
    /**
     * StudentAttendanceSettingController constructor.
     * @param StudentAttendanceSetting $sas
     */
    function __construct(StudentAttendanceSetting $sas) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->sas = $sas;
        $this->approve($sas);
        
    }
    
    /**
     * 学生考勤设置列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->sas->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学生考勤设置
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存学生考勤设置
     *
     * @param StudentAttendanceSettingRequest $request
     * @return JsonResponse
     */
    public function store(StudentAttendanceSettingRequest $request) {
        
        return $this->result(
            $this->sas->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑学生考勤设置
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'sas' => $this->sas->find($id),
        ]);
        
    }
    
    /**
     * 更新学生考勤设置
     *
     * @param StudentAttendanceSettingRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(StudentAttendanceSettingRequest $request, $id) {
        
        return $this->result(
            $this->sas->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除学生考勤设置
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->sas->remove($id)
        );
        
    }
    
}
