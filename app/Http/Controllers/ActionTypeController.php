<?php
namespace App\Http\Controllers;

use App\Http\Requests\ActionTypeRequest;
use App\Models\ActionType;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(ActionType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        
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
                $this->at->datatable()
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
        
        $this->authorize(
            'cs', ActionType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存Http请求类型
     *
     * @param ActionTypeRequest $request
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function store(ActionTypeRequest $request) {
        
        $this->authorize(
            'cs', ActionType::class
        );
        
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
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function edit($id) {
        
        $at = $this->at->find($id);
        $this->authorize('eud', $at);
        
        return $this->output([
            'at' => $at
        ]);
        
    }
    
    /**
     * 更新Http请求类型
     *
     * @param ActionType $request
     * @param $id
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function update(ActionType $request, $id) {
    
        $at = $this->at->find($id);
        $this->authorize('eud', $at);
        
        return $this->result(
            $at->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除Http请求类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function destroy($id) {
    
        $at = $this->at->find($id);
        $this->authorize('eud', $at);
    
        return $this->result(
            $at->remove($id)
        );
    
    }
    
}
