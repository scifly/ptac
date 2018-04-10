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
    
    protected $user;
    
    function __construct(User $user) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->user = $user;
        
    }
    
    /**
     * 超级管理员列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->user->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建超级管理员
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'create', User::class
        );
        if (Request::method() == 'POST') {
            return $this->user->csList();
        }
        
        return $this->output();
        
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
        
        $this->authorize(
            'store', User::class
        );
        
        return $this->result(
            $this->user->store($request->all())
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
        
        $user = $this->user->find($id);
        $this->authorize('edit', $user);
        
        if (Request::method() == 'POST') {
            return $this->user->csList();
        }
        
        return $this->output([
            'user' => $user,
        ]);
        
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
        
        $this->authorize(
            'update', User::class
        );
        
        return $this->result(
            $this->user->modify(
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
    
        $this->authorize(
            'delete', User::class
        );
        
        return $this->result(
            $this->user->remove($id)
        );
        
    }
    
}
