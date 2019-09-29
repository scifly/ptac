<?php
namespace App\Http\Controllers;

use App\Http\Requests\OperatorRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 超级用户
 *
 * Class OperatorController
 * @package App\Http\Controllers
 */
class OperatorController extends Controller {
    
    protected $operator;
    
    /**
     * OperatorController constructor.
     * @param User $operator
     */
    function __construct(User $operator) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->operator = $operator);
        
    }
    
    /**
     * 超级管理员列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->operator->index())
            : $this->output();
        
    }
    
    /**
     * 创建超级管理员
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->operator->csList()
            : $this->output();
        
    }
    
    /**
     * 保存超级管理员
     *
     * @param OperatorRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     * @throws Exception
     */
    public function store(OperatorRequest $request) {
        
        return $this->result(
            $this->operator->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑超级管理员
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->operator->csList()
            : $this->output(['operator' => $this->operator->find($id)]);
        
    }
    
    /**
     * 更新超级管理员
     *
     * @param OperatorRequest $request
     * @param $id
     * @return JsonResponse|string
     * @throws Exception
     * @throws Throwable
     */
    public function update(OperatorRequest $request, $id = null) {
        
        return $this->result(
            $this->operator->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除超级管理员
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->operator->remove($id)
        );
        
    }
    
}
