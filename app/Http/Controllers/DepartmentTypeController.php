<?php
namespace App\Http\Controllers;

use App\Http\Requests\DepartmentTypeRequest;
use App\Models\DepartmentType;
use Exception;
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

    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);

    }
    
    /**
     * 部门类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json(DepartmentType::datatable());
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

        return $this->result(DepartmentType::store($request->all()));

    }
    
    /**
     * 编辑部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {

        $departmentType = DepartmentType::find($id);
        if (!$departmentType) { return $this->notFound(); }

        return $this->output([
            'departmentType' => $departmentType
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

        $departmentType = DepartmentType::find($id);
        if (!$departmentType) { return $this->notFound(); }

        return $this->result($departmentType::modify($request->all(), $id));

    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {

        $departmentType = DepartmentType::find($id);
        if (!$departmentType) { return $this->notFound(); }

        return $this->result(departmentType::remove($id));

    }

}
