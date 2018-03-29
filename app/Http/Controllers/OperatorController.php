<?php
namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\User;
use App\Helpers\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\OperatorRequest;
use Illuminate\Support\Facades\Request;

/**
 * 超级用户管理
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
            'user' => $user
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
    public function update(OperatorRequest $request, $id) {
        
        $user = $this->user->find($id);
        abort_if(!$user, HttpStatusCode::NOT_FOUND, '找不到该用户记录');
        
        return $this->result(
            $user->modify(
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
    public function destroy($id) {
    
        $user = $this->user->find($id);
        $this->authorize('destroy', $user);
        return $this->result(
            $user->remove($id)
        );
    
    }
    
}
