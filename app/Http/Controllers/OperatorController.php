<?php
namespace App\Http\Controllers;


use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

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
        
        return $this->output();
        
    }
    
    /**
     * 保存超级管理员
     *
     * @param UserRequest $request
     * @return JsonResponse|string
     */
    public function store(UserRequest $request) {
        
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
        
        return $this->output([
            'user' => $user
        ]);
        
        
    }
    
    /**
     * 更新超级管理员
     *
     * @param UserRequest $request
     * @param $id
     * @return JsonResponse|string
     */
    public function update(UserRequest $request, $id) {
        
        $user = $this->user->find($id);
        
        return $this->result(
            $user->modify($request->all(), $id)
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
        
        return $this->result(
            $user->remove($id)
        );
    
    }
    
}
