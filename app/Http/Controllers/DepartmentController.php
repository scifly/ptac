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
    
    protected $dept;
    
    /**
     * DepartmentController constructor.
     * @param Department $dept
     */
    function __construct(Department $dept) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->dept = $dept);
        
    }
    
    /**
     * 部门列表.排序.移动
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::method() === 'POST'
            ? $this->dept->index()
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
     * @throws Throwable
     */
    public function store(DepartmentRequest $request) {
        
        return $this->result(
            $this->dept->store(
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
            'department' => $this->dept->find($id),
        ]);
        
    }
    
    /**
     * 更新部门
     *
     * @param DepartmentRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(DepartmentRequest $request, $id) {
        
        return $this->result(
            $this->dept->modify(
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
            $this->dept->remove($id)
        );
        
    }
    
}