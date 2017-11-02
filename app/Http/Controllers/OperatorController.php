<?php
namespace App\Http\Controllers;

use App\Http\Requests\OperatorRequest;
use App\Models\Department;
use App\Models\Operator;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

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
        
        $this->operator = $operator;
        $this->department = $department;
        $this->school = $school;
        
    }
    
    /**
     * 系统管理员列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->operator->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建系统管理员
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return $this->department->tree(Request::query('rootId'));
        }
        return $this->output(__METHOD__, [
            'role' => Auth::user()->group->name
        ]);
        
    }
    
    /**
     * 保存系统管理员
     *
     * @param OperatorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OperatorRequest $request) {
        
        return $this->operator->store($request)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 系统管理员详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $operator = $this->operator->find($id);
        if (!$operator) { return $this->notFound();}
        
        return $this->output(__METHOD__, [
            'operator' => $operator,
            'role' => Auth::user()->group->name
        ]);
        
    }
    
    /**
     * 编辑系统管理员
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        $operator = $this->operator->find($id);
        if (!$operator) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['operator' => $operator]);
        
    }
    
    /**
     * 更新系统管理员
     *
     * @param OperatorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OperatorRequest $request, $id) {
        
        $operator = $this->operator->find($id);
        if (!$operator) { return $this->notFound(); }
        
        return $this->operator->modify($request, $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除系统管理员
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $operator = $this->operator->find($id);
        if (!$operator) { return $this->notFound(); }
        
        return $this->operator->remove($id)
            ? $this->succeed('删除成功') : $this->fail('无法删除');
        
    }
    
}
