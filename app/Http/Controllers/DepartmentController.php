<?php
namespace App\Http\Controllers;

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
    
    protected $department, $departmentType;
    
    function __construct(Department $department, DepartmentType $departmentType) {
    
        $this->middleware(['auth']);
        $this->department = $department;
        $this->departmentType = $departmentType;
        
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
        
        $departmentTypeId = DepartmentType::whereName('其他')->first()->id;

        return $this->output([
            'parentId' => $id,
            'departmentTypeId' => $departmentTypeId
        ]);
        
    }
    
    /**
     * 保存部门
     *
     * @param DepartmentRequest $request
     * @return JsonResponse
     */
    public function store(DepartmentRequest $request) {
        
        return $this->department->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
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
        if (!$department) {
            return $this->notFound();
        }

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
        if (!$department) {
            return $this->notFound();
        }

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
        
        if (!$this->department->find($id)) {
            return $this->notFound();
        }

        return $this->department->modify($request->all(), $id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除部门
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        if (!$this->department->find($id)) {
            return $this->notFound();
        }

        return $this->department->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 更新部门所处位置
     *
     * @param $id
     * @param $parentId
     * @return JsonResponse
     */
    public function move($id, $parentId = null) {
        
        if (!$parentId) {
            return $this->fail('非法操作');
        }
        $department = $this->department->find($id);
        $parentDepartment = $this->department->find($parentId);
        if (!$department || !$parentDepartment) {
            return parent::notFound();
        }
        if ($this->department->movable($id, $parentId)) {
            return $this->department->move($id, $parentId, true)
                ? parent::succeed() : parent::fail();
        }

        return $this->fail('非法操作');
        
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


    /**
     * 获取该部门下所有部门id
     * @param $id
     * @return array
     */
    private function departmentChildIds($id) {
        static $childIds = [];
        $firstIds = Department::where('parent_id', $id)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childIds[] = $firstId['id'];
                $this->departmentChildIds($firstId['id']);
            }
        }

        return $childIds;
    }
    
}
