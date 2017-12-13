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
    
    protected $examType;
    
    function __construct(ExamType $examType) {
    
        $this->middleware(['auth']);
        $this->examType = $examType;
        
    }
    
    /**
     * 考试类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->examType->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建考试类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存考试类型
     *
     * @param ExamTypeRequest $request
     * @return JsonResponse
     */
    public function store(ExamTypeRequest $request) {
        
        return $this->examType->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑考试类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        
        return $this->output(__METHOD__, [
            'examType' => $examType
        ]);
        
    }
    
    /**
     * 更新考试类型
     *
     * @param ExamTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ExamTypeRequest $request, $id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        
        return $examType->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) { return $this->notFound(); }
        
        return $examType->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}
