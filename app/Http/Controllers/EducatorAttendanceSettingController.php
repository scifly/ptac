<?php
namespace App\Http\Controllers;

use App\Http\Requests\EducatorAttendanceSettingRequest;
use App\Models\EducatorAttendanceSetting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 教职员工考勤设置
 *
 * Class EducatorAttendanceSettingController
 * @package App\Http\Controllers
 */
class EducatorAttendanceSettingController extends Controller {
    
    protected $eas;
    
    /**
     * EducatorAttendanceSettingController constructor.
     * @param EducatorAttendanceSetting $eas
     */
    function __construct(EducatorAttendanceSetting $eas) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->eas = $eas;
        $this->approve($eas);
        
    }
    
    /**
     * 教职员工考勤设置列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->eas->index()
            );
        }
        
        return $this->output();
    }
    
    /**
     * 创建教职工考勤设置
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存教职工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @return JsonResponse
     */
    public function store(EducatorAttendanceSettingRequest $request) {
        
        return $this->result(
            $this->eas->store($request->all())
        );
        
    }
    
    /**
     * 编辑教职员工考勤设置
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'eas' => $this->eas->find($id),
        ]);
        
    }
    
    /**
     * 更新教职员工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(EducatorAttendanceSettingRequest $request, $id) {
        
        return $this->result(
            $this->eas->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除教职员工考勤设置
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->eas->remove($id)
        );
        
    }
    
}
