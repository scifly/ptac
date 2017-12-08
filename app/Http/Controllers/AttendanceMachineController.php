<?php
namespace App\Http\Controllers;

use App\Http\Requests\AttendanceMachineRequest;
use App\Models\AttendanceMachine;
use Exception;
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
    
        $this->middleware(['auth']);
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
            return response()->json($this->am->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建考勤机记录
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存考勤机记录
     *
     * @param AttendanceMachineRequest $request
     * @return JsonResponse
     */
    public function store(AttendanceMachineRequest $request) {

        return $this->am->create($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 考勤机详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function show($id) {
        
        $am = $this->am->find($id);
        if (!$am) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['am' => $am]);
        
    }
    
    /**
     * 编辑考勤机记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $am = $this->am->find($id);
        if (!$am) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['am' => $am]);
        
    }
    
    /**
     * 更新考勤机记录
     *
     * @param AttendanceMachineRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(AttendanceMachineRequest $request, $id) {
        
        $am = $this->am->find($id);
        if (!$am) { return $this->notFound(); }
        
        return $am->update($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除考勤机记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $am = $this->am->find($id);
        if (!$am) { return $this->notFound(); }
        
        return $am->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
