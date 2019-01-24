<?php
namespace App\Http\Controllers;

use App\Helpers\Operator;
use App\Http\Requests\OperatorRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;
use Way\Generators\Commands\ModelGeneratorCommand;

/**
 * 超级用户
 *
 * Class OperatorController
 * @package App\Http\Controllers
 */
class OperatorController extends Controller {
    
    protected $user;
    
    /**
     * OperatorController constructor.
     * @param User $user
     */
    function __construct(User $user) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->user = $user;
        Request::has('ids') ?: $this->approve($user);
        
    }
    
    /**
     * 超级管理员列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->user->index())
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
            ? $this->user->csList()
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
            $this->user->store(
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
        
        if (Request::method() == 'POST') {
            return $this->user->csList();
        }
        $operator = new Operator();
        $operator->{'user'} = $this->user->find($id);

        return $this->output(['operator' => $operator]);
        
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
        
        return $this->result(
            $this->user->remove($id)
        );
        
    }
    
}
