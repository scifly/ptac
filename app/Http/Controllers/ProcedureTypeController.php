<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureTypeRequest;
use App\Models\ProcedureType;
use Illuminate\Support\Facades\Request;

/**
 * 审批流程类型
 *
 * Class ProcedureTypeController
 * @package App\Http\Controllers
 */
class ProcedureTypeController extends Controller {

    protected $procedureType;

    function __construct(ProcedureType $procedureType) {

        $this->procedureType = $procedureType;

    }

    /**
     * 审批流程类型列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->procedureType->datatable());
        }

        return $this->output(__METHOD__);

    }

    /**
     * 创建审批流程类型
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        return $this->output(__METHOD__);

    }

    /**
     * 创建审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureTypeRequest $request) {

        return $this->procedureType->create($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 审批流程类型详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, ['procedureType' => $procedureType]);

    }

    /**
     * 编辑审批流程类型
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, ['procedureType' => $procedureType]);

    }

    /**
     * 更新审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureTypeRequest $request, $id) {

        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }

        return $procedureType->update($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除审批流程类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }

        return $procedureType->delete() ? $this->succeed() : $this->fail();

    }

}
