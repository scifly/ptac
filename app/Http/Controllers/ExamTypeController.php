<?php
namespace App\Http\Controllers;

use App\Http\Requests\ExamTypeRequest;
use App\Models\ExamType;
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
    
    /**
     * ExamTypeController constructor.
     * @param ExamType $et
     */
    function __construct(ExamType $et) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->et = $et;
        $this->approve($et);
        
    }
    
    /**
     * 考试类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->et->index())
            : $this->output();
        
    }
    
    /**
     * 创建考试类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存考试类型
     *
     * @param ExamTypeRequest $request
     * @return JsonResponse
     */
    public function store(ExamTypeRequest $request) {
        
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
        
        return $this->output([
            'et' => ExamType::find($id),
        ]);
        
    }
    
    /**
     * 更新考试类型
     *
     * @param ExamTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(ExamTypeRequest $request, $id) {
        
        return $this->result(
            $this->et->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->et->remove($id)
        );
        
    }
    
}
