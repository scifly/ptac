<?php
namespace App\Http\Controllers;

use App\Http\Requests\EducatorAttendanceSettingRequest;
use App\Models\EducatorAttendanceSetting;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
        
        $this->middleware(['auth', 'checkrole']);
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
            return response()->json(
                $this->eas->datatable()
            );
        }
        
        return $this->output();
    }
    
    /**
     * 创建教职工考勤设置
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        $this->authorize(
            'create',
            EducatorAttendanceSetting::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存教职工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(EducatorAttendanceSettingRequest $request) {
        
        $this->authorize(
            'store',
            EducatorAttendanceSetting::class
        );
        
        return $this->result(
            $this->eas->store($request->all())
        );
        
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
        $this->authorize('show', $eas);
        
        return $this->output(['eas' => $eas]);
        
    }
    
    /**
     * 编辑教职员工考勤设置
     * @param $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $eas = $this->eas->find($id);
        $this->authorize('edit', $eas);
        
        return $this->output(['eas' => $eas]);
        
    }
    
    /**
     * 更新教职员工考勤设置
     *
     * @param EducatorAttendanceSettingRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(EducatorAttendanceSettingRequest $request, $id) {
        
        $eas = $this->eas->find($id);
        $this->authorize('update', $eas);
        
        return $this->result(
            $eas->update($request->all())
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
        
        $eas = $this->eas->find($id);
        $this->authorize('destroy', $eas);
        
        return $this->result(
            $eas->delete()
        );
        
    }
    
}
