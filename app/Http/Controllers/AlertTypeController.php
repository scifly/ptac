<?php
namespace App\Http\Controllers;

use App\Http\Requests\AlertTypeRequest;
use App\Models\AlertType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 警告类型
 *
 * Class AlertTypeController
 * @package App\Http\Controllers
 */
class AlertTypeController extends Controller {
    
    protected $at;
    
    /**
     * AlertTypeController constructor.
     * @param AlertType $at
     */
    function __construct(AlertType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        $this->approve($at);
        
    }
    
    /**
     * 警告类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->at->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建警告类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存警告类型
     *
     * @param AlertTypeRequest $request
     * @return JsonResponse
     */
    public function store(AlertTypeRequest $request) {
        
        return $this->result(
            $this->at->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'at' => $this->at->find($id),
        ]);
        
    }
    
    /**
     * 更新警告类型
     *
     * @param AlertTypeRequest $request
     * @param $id
     * @return bool|JsonResponse
     */
    public function update(AlertTypeRequest $request, $id) {
        
        return $this->result(
            $this->at->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->at->remove($id)
        );
        
    }
    
}
