<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\OperatorRequest;
use App\Models\Department;
use App\Models\Operator;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 系统管理员
 *
 * Class OperatorController
 * @package App\Http\Controllers
 */
class OperatorController extends Controller {
    
    protected $operator;
    protected $department;
    protected $school;
    
    function __construct(Operator $operator, Department $department, School $school) {
    
        $this->middleware(['auth']);
        $this->operator = $operator;
        $this->department = $department;
        $this->school = $school;
        
    }
    
    /**
     * 系统管理员列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->operator->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建系统管理员
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return response()->json(
                $this->department->tree(Request::query('rootId'))
            );
        }
        
        return $this->output([
            'role' => Auth::user()->group->name
        ]);
        
    }
    
    /**
     * 保存系统管理员
     *
     * @param OperatorRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(OperatorRequest $request) {
        
        return $this->result(
            $this->operator->store($request)
        );
        
    }
    
    /**
     * 系统管理员详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $operator = $this->operator->find($id);
        abort_if(!$operator, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'operator' => $operator,
            'role' => Auth::user()->group->name
        ]);
        
    }
    
    /**
     * 编辑系统管理员
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        if (Request::method() === 'POST') {
            return response()->json($this->department->tree());
        }
        $operator = $this->operator->find($id);
        abort_if(!$operator, HttpStatusCode::NOT_FOUND);
        
        return $this->output(['operator' => $operator]);
        
    }
    
    /**
     * 更新系统管理员
     *
     * @param OperatorRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(OperatorRequest $request, $id) {
        
        $operator = $this->operator->find($id);
        abort_if(!$operator, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $operator->modify($request, $id)
        );
        
    }
    
    /**
     * 删除系统管理员
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $operator = $this->operator->find($id);
        abort_if(!$operator, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $operator->remove($id)
        );
        
    }
    
}
