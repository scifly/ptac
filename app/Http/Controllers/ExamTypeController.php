<?php
namespace App\Http\Controllers;

use App\Http\Requests\ExamTypeRequest;
use App\Models\ExamType;
use Illuminate\Support\Facades\Request;

/**
 * 考试类型
 *
 * Class ExamTypeController
 * @package App\Http\Controllers
 */
class ExamTypeController extends Controller {
    
    protected $examType;
    
    function __construct(ExamType $examType) { $this->examType = $examType; }
    
    /**
     * 考试类型列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
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
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存考试类型
     *
     * @param ExamTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExamTypeRequest $request) {
        
        return $this->examType->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 考试类型详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, [
            'examType' => $examType,
        ]);
        
    }
    
    /**
     * 编辑考试类型
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['examType' => $examType]);
        
    }
    
    /**
     * 更新考试类型
     *
     * @param ExamTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExamTypeRequest $request, $id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) {
            return $this->notFound();
        }
        
        return $examType->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $examType = $this->examType->find($id);
        if (!$examType) {
            return $this->notFound();
        }
        
        return $examType->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}
