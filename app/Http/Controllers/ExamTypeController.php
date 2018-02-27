<?php
namespace App\Http\Controllers;

use App\Http\Requests\ExamTypeRequest;
use App\Models\ExamType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 考试类型
 *
 * Class ExamTypeController
 * @package App\Http\Controllers
 */
class ExamTypeController extends Controller {
    
    protected $et;
    
    function __construct(ExamType $et) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->et = $et;
        
    }
    
    /**
     * 考试类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->et->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建考试类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize('c', ExamType::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存考试类型
     *
     * @param ExamTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ExamTypeRequest $request) {
     
        $this->authorize('c', ExamType::class);
        
        return $this->result(
            $this->et->store($request->all())
        );
        
    }
    
    /**
     * 编辑考试类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $et = ExamType::find($id);
        $this->authorize('rud', $et);
        
        return $this->output(['et' => $et]);
        
    }
    
    /**
     * 更新考试类型
     *
     * @param ExamTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ExamTypeRequest $request, $id) {
        
        $et = ExamType::find($id);
        $this->authorize('rud', $et);
        
        return $this->result(
            $et->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy($id) {
        
        $et = ExamType::find($id);
        $this->authorize('rud', $et);
        
        return $this->result($et->remove($id));
        
    }
    
}
