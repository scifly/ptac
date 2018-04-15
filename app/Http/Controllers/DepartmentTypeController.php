<?php
namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\DepartmentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\DepartmentTypeRequest;

/**
 * 部门类型
 *
 * Class DepartmentTypeController
 * @package App\Http\Controllers
 */
class DepartmentTypeController extends Controller {
    
    protected $dt;
    
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
        
        if (Request::get('draw')) {
            return response()->json(
                $this->dt->datatable()
            );
        }
        
        return $this->output();
        
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
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->dt->remove($id)
        );
        
    }
    
}