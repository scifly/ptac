<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\Message;
use App\Models\School as School;
use Exception;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学校
 *
 * Class SchoolController
 * @package App\Http\Controllers
 */
class SchoolController extends Controller {
    
    protected $school, $message;
    
    /**
     * SchoolController constructor.
     * @param School $school
     * @param Message $message
     */
    function __construct(School $school, Message $message) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->school = $school;
        $this->message = $message;
        Request::has('ids') ?: $this->approve($school);
        
    }
    
    /**
     * 学校列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->school->index())
            : $this->output();
        
    }
    
    /**
     * 创建学校
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存学校
     *
     * @param SchoolRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store(SchoolRequest $request) {
        
        return $this->result(
            $this->school->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑学校
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'school' => School::find($id),
        ]);
        
    }
    
    /**
     * 更新学校
     *
     * @param SchoolRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(SchoolRequest $request, $id = null) {
        
        return $this->result(
            $this->school->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 短信充值 & 查询
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function recharge($id) {
        
        return Request::get('draw')
            ? response()->json($this->message->sms('school', $id))
            : (
            Request::method() == 'PUT'
                ? $this->school->recharge($id, Request::all())
                : $this->output(['school' => $this->school->find($id)])
            );
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->school->remove($id)
        );
        
    }
    
}
