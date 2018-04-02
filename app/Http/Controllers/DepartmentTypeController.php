<?php
namespace App\Http\Controllers;

use App\Http\Requests\DepartmentTypeRequest;
use App\Models\DepartmentType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(DepartmentType $dt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->dt = $dt;
        
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
        
        $this->authorize(
            'cs', DepartmentType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存部门类型
     *
     * @param DepartmentTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(DepartmentTypeRequest $request) {
        
        $this->authorize(
            'cs', DepartmentType::class
        );
        
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
        
        $dt = $this->dt->find($id);
        $this->authorize('eud', $dt);
        
        return $this->output([
            'departmentType' => $dt,
        ]);
        
    }
    
    /**
     * 更新部门类型
     *
     * @param DepartmentTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(DepartmentTypeRequest $request, $id) {
        
        $dt = $this->dt->find($id);
        $this->authorize('eud', $dt);
        
        return $this->result(
            $dt->modify($request->all(), $id)
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
        
        $dt = $this->dt->find($id);
        $this->authorize('eud', $dt);
        
        return $this->result(
            $dt->remove($id)
        );
        
    }
    
}
