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
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 部门列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::method() === 'POST') {
            return response()->json(Department::tree());
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
            Department::store($request->all(), true)
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
        
        $department = Department::find($id);

        return $this->output(['department' => $department]);
        
    }
    
    /**
     * 编辑部门
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $department = Department::find($id);

        return $this->output(['department' => $department]);
        
    }
    
    /**
     * 更新部门
     *
     * @param DepartmentRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(DepartmentRequest $request, $id) {
        
        $department = Department::find($id);

        return $this->result(
            $department::modify($request->all(), $id, true)
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
        
        $department = Department::find($id);

        return $this->result($department::remove($id));
        
    }
    
    /**
     * 更新部门所处位置
     *
     * @param $id
     * @param $parentId
     * @return JsonResponse
     */
    public function move($id, $parentId = null) {
        
        if (!$parentId) { return $this->fail('非法操作'); }
        $department = Department::find($id);
        $parentDepartment = Department::find($parentId);
        if (!$department || !$parentDepartment) {
            return $this->notFound();
        }
        if ($department::movable($id, $parentId)) {
            return $this->result(
                $department::move($id, $parentId, true)
            );
        }

        return $this->fail('非法操作');
        
    }
    
    /**
     * 保存部门的排列顺序
     */
    public function sort() {
        
        $orders = Request::get('data');
        foreach ($orders as $id => $order) {
            $department = Department::find($id);
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
    private static function departmentChildrenIds($id) {
        
        static $childrenIds = [];
        $firstIds = Department::whereParentId($id)
            ->pluck('id')
            ->toArray();
        if ($firstIds) {
            foreach ($firstIds as $id) {
                $childrenIds[] = $id['id'];
                self::departmentChildrenIds($id['id']);
            }
        }

        return $childrenIds;
        
    }
    
}
