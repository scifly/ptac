<?php
namespace App\Http\Controllers;

use App\Http\Requests\DepartmentTypeRequest;
use App\Models\DepartmentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 部门类型
 *
 * Class DepartmentTypeController
 * @package App\Http\Controllers
 */
class DepartmentTypeController extends Controller {
    
    protected $dt;
    
    /**
     * DepartmentTypeController constructor.
     * @param DepartmentType $dt
     */
    function __construct(DepartmentType $dt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->dt = $dt;
        $this->approve($dt);
        
    }
    
    /**
     * 部门类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->dt->index())
            : $this->output();
        
    }
    
    /**
     * 创建部门类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存部门类型
     *
     * @param DepartmentTypeRequest $request
     * @return JsonResponse
     */
    public function store(DepartmentTypeRequest $request) {
        
        return $this->result(
            $this->dt->store($request->all())
        );
        
    }
    
    /**
     * 编辑部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'dt' => $this->dt->find($id),
        ]);
        
    }
    
    /**
     * 更新部门类型
     *
     * @param DepartmentTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(DepartmentTypeRequest $request, $id) {
        
        return $this->result(
            $this->dt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->dt->remove($id)
        );
        
    }
    
}