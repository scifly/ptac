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

    protected $departmentType;

    function __construct(DepartmentType $departmentType) {
    
        $this->middleware(['auth']);
        $this->departmentType = $departmentType;

    }
    
    /**
     * 部门类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->departmentType->datatable());
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

        return $this->departmentType->store($request->all())
            ? $this->succeed() : $this->fail();

    }
    
    /**
     * 编辑部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {

        $departmentType = $this->departmentType->find($id);
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

        if (!$this->departmentType->find($id)) { return $this->notFound(); }

        return $this->departmentType->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();

    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {

        if (!$this->departmentType->find($id)) {
            return $this->notFound();
        }

        return $this->departmentType->remove($id)
            ? $this->succeed() : $this->fail();

    }

}
