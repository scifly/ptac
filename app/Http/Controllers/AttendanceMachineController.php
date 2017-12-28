<?php
namespace App\Http\Controllers;

use App\Http\Requests\AttendanceMachineRequest;
use App\Models\AttendanceMachine;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 考勤机
 *
 * Class AttendanceMachineController
 * @package App\Http\Controllers
 */
class AttendanceMachineController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
    
    }
    
    /**
     * 考勤机列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json(AttendanceMachine::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建考勤机记录
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        $this->authorize('c', AttendanceMachine::class);
        return $this->output();
        
    }
    
    /**
     * 保存考勤机记录
     *
     * @param AttendanceMachineRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AttendanceMachineRequest $request) {

        $this->authorize('c', AttendanceMachine::class);
        return $this->result(AttendanceMachine::create($request->all()));
        
    }
    
    /**
     * 编辑考勤机记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $am = AttendanceMachine::find($id);
        $this->authorize('rud', $am);
        
        return $this->output(['am' => $am]);
        
    }
    
    /**
     * 更新考勤机记录
     *
     * @param AttendanceMachineRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AttendanceMachineRequest $request, $id) {
        
        $am = AttendanceMachine::find($id);
        $this->authorize('rud', $am);
        
        return $this->result($am->update($request->all()));
        
    }
    
    /**
     * 删除考勤机记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $am = AttendanceMachine::find($id);
        $this->authorize('rud', $am);
        
        return $this->result($am->delete());
        
    }
    
}
