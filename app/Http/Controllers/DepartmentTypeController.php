<?php
namespace App\Http\Controllers;

use App\Http\Requests\DepartmentTypeRequest;
use App\Models\DepartmentType;
use Illuminate\Support\Facades\Request;

/**
 * 部门类型
 *
 * Class DepartmentTypeController
 * @package App\Http\Controllers
 */
class DepartmentTypeController extends Controller {

    protected $departmentType;

    function __construct(DepartmentType $departmentType) {
    
        $this->middleware(['auth']);
        $this->departmentType = $departmentType;

    }

    /**
     * 部门类型列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->departmentType->datatable());
        }

        return $this->output(__METHOD__);

    }

    /**
     * 创建部门类型
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {

        return $this->output(__METHOD__);

    }

    /**
     * 保存部门类型
     *
     * @param DepartmentTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentTypeRequest $request) {

        return $this->departmentType->store($request->all())
            ? $this->succeed() : $this->fail();

    }

    /**
     * 编辑部门类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $departmentType = $this->departmentType->find($id);
        if (!$departmentType) { return $this->notFound(); }

        return $this->output(__METHOD__, [
            'departmentType' => $departmentType
        ]);

    }

    /**
     * 更新部门类型
     *
     * @param DepartmentTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentTypeRequest $request, $id) {

        if (!$this->departmentType->find($id)) { return $this->notFound(); }

        return $this->departmentType->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();

    }

    /**
     * 删除部门类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        if (!$this->departmentType->find($id)) {
            return $this->notFound();
        }

        return $this->departmentType->remove($id)
            ? $this->succeed() : $this->fail();

    }

}
