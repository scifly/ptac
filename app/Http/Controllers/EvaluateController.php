<?php
namespace App\Http\Controllers;

use App\Http\Requests\EvaluateRequest;
use App\Models\Evaluate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 考核记录
 *
 * Class EvaluateController
 * @package App\Http\Controllers
 */
class EvaluateController extends Controller {
    
    protected $eval;
    
    /**
     * EvaluateController constructor.
     * @param Evaluate $eval
     */
    function __construct(Evaluate $eval) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->eval = $eval);
        
    }
    
    /**
     * 考核记录列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->eval->index())
            : $this->output();
        
    }
    
    /**
     * 创建考核记录
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存考核记录
     *
     * @param EvaluateRequest $request
     * @return JsonResponse
     */
    public function store(EvaluateRequest $request) {
        
        return $this->result(
            $this->eval->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑考核记录
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'evaluate' => $this->eval->find($id),
        ]);
        
    }
    
    /**
     * 更新考核记录
     *
     * @param EvaluateRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(EvaluateRequest $request, $id) {
        
        return $this->result(
            $this->eval->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除考核记录
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->eval->remove($id)
        );
        
    }
    
}
