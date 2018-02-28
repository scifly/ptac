<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\DepartmentType;
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

    function __construct(Department $department) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->department = $department;
        
    }
    
    /**
     * 部门列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::method() === 'POST') {
            return response()->json($this->department->tree());
        }

        return $this->output();
        
    }
    
    /**
     * 创建部门
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create($id) {
        
        return $this->output([
            'parentId' => $id,
            'departmentTypeId' => DepartmentType::whereName('其他')->first()->id
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
                $request->all(), true
            )
        );
        
    }
    
    /**
     * 部门详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $department = $this->department->find($id);
        abort_if(!$department, HttpStatusCode::NOT_FOUND);

        return $this->output([
            'department' => $department,
        ]);
        
    }
    
    /**
     * 编辑部门
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $department = $this->department->find($id);
        abort_if(!$department, HttpStatusCode::NOT_FOUND);

        return $this->output([
            'department' => $department,
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
        
        $department = $this->department->find($id);
        abort_if(!$department, HttpStatusCode::NOT_FOUND);

        return $this->result(
            $department->modify($request->all(), $id, true)
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
        
        $department = $this->department->find($id);
        abort_if(!$department, HttpStatusCode::NOT_FOUND);

        return $this->result($department->remove($id));
        
    }
    
    /**
     * 更新部门所处位置
     *
     * @param $id
     * @param $parentId
     * @return JsonResponse
     */
    public function move($id, $parentId = null) {
        
        $department = $this->department->find($id);
        $parentDepartment = $this->department->find($parentId);
        abort_if(!$department || !$parentDepartment, HttpStatusCode::NOT_FOUND);
        if ($department->movable($id, $parentId)) {
            return $this->result(
                $department->move($id, $parentId, true)
            );
        }

        return abort(HttpStatusCode::NOT_ACCEPTABLE);
        
    }
    
    /**
     * 保存部门的排列顺序
     */
    public function sort() {
        
        $orders = Request::get('data');
        foreach ($orders as $id => $order) {
            $department = $this->department->find($id);
            if (isset($department)) {
                $department->order = $order;
                $department->save();
            }
        }
        
    }

}
