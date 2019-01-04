<?php
namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 部门
 *
 * Class DepartmentController
 * @package App\Http\Controllers
 */
class DepartmentController extends Controller {
    
    protected $department;
    
    /**
     * DepartmentController constructor.
     * @param Department $department
     */
    function __construct(Department $department) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->department = $department;
        $this->approve($department);
        
    }
    
    /**
     * 部门列表.排序.移动
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::method() === 'POST'
            ? $this->department->index()
            : $this->output();
        
    }
    
    /**
     * 创建部门
     *
     * @param $parentId
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create($parentId) {
        
        return $this->output([
            'parentId' => $parentId,
        ]);
        
    }
    
    /**
     * 保存部门
     *
     * @param DepartmentRequest $request
     * @return JsonResponse
     */
    public function store(DepartmentRequest $request) {
        
        return $this->result(
            $this->department->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑部门
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'department' => $this->department->find($id),
        ]);
        
    }
    
    /**
     * 更新部门
     *
     * @param DepartmentRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(DepartmentRequest $request, $id) {
        
        return $this->result(
            $this->department->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除部门
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->department->remove($id),
            __('messages.ok'),
            __('messages.department.has_children')
        );
        
    }
    
}