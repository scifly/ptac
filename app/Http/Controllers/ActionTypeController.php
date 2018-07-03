<?php
namespace App\Http\Controllers;

use App\Http\Requests\ActionTypeRequest;
use App\Models\ActionType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Http请求类型
 *
 * Class ActionTypeController
 * @package App\Http\Controllers
 */
class ActionTypeController extends Controller {
    
    protected $at;
    
    /**
     * ActionTypeController constructor.
     * @param ActionType $at
     */
    function __construct(ActionType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        $this->approve($at);
        
    }
    
    /**
     * Http请求类型列表
     *
     * @return bool|JsonResponse
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
     * 创建Http请求类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存Http请求类型
     *
     * @param ActionTypeRequest $request
     * @return JsonResponse|string
     */
    public function store(ActionTypeRequest $request) {
        
        return $this->result(
            $this->at->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑Http请求类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'at' => $this->at->find($id),
        ]);
        
    }
    
    /**
     * 更新Http请求类型
     *
     * @param ActionType $request
     * @param $id
     * @return JsonResponse|string
     */
    public function update(ActionType $request, $id) {
        
        return $this->result(
            $this->at->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除Http请求类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->at->remove($id)
        );
        
    }
    
}
