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
    
    protected $am;
    
    function __construct(AttendanceMachine $am) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->am = $am;
        
    }
    
    /**
     * 考勤机列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->am->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建考勤机
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        $this->authorize(
            'c', AttendanceMachine::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存考勤机
     *
     * @param AttendanceMachineRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AttendanceMachineRequest $request) {
        
        $this->authorize(
            'c', AttendanceMachine::class
        );
        
        return $this->result(
            $this->am->create($request->all())
        );
        
    }
    
    /**
     * 编辑考勤机
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $am = $this->am->find($id);
        $this->authorize('rud', $am);
        
        return $this->output(['am' => $am]);
        
    }
    
    /**
     * 更新考勤机
     *
     * @param AttendanceMachineRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AttendanceMachineRequest $request, $id) {
        
        $am = $this->am->find($id);
        $this->authorize('rud', $am);
        
        return $this->result(
            $am->update($request->all())
        );
        
    }
    
    /**
     * 删除考勤机
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $am = $this->am->find($id);
        $this->authorize('rud', $am);
        
        return $this->result($am->delete());
        
    }
    
}
