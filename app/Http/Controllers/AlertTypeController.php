<?php
namespace App\Http\Controllers;

use App\Http\Requests\AlertTypeRequest;
use App\Models\AlertType;
use Illuminate\Support\Facades\Request;

/**
 * 警告类型
 *
 * Class AlertTypeController
 * @package App\Http\Controllers
 */
class AlertTypeController extends Controller {

    protected $alertType;

    function __construct(AlertType $alertType) {

        $this->alertType = $alertType;

    }

    /**
     * 警告类型列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->alertType->datatable());
        }

        return $this->output(__METHOD__);

    }

    /**
     * 创建警告类型
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {

        return $this->output(__METHOD__);

    }

    /**
     * 保存警告类型
     *
     * @param AlertTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AlertTypeRequest $request) {

        return $this->alertType->create($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 警告类型详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $alertType = $this->alertType->find($id);
        if (!$alertType) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, ['alertType' => $alertType]);

    }

    /**
     * 编辑警告类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {

        $alertType = $this->alertType->find($id);
        if (!$alertType) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, ['alertType' => $alertType]);

    }

    /**
     * 更新警告类型
     *
     * @param AlertTypeRequest $request
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(AlertTypeRequest $request, $id) {

        $alertType = $this->alertType->find($id);
        if (!$alertType) {
            return $this->notFound();
        }

        return $alertType->update($request->all());

    }

    /**
     * 删除警告类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $alertType = $this->alertType->find($id);
        if (!$alertType) {
            return $this->notFound();
        }

        return $alertType->delete() ? $this->succeed() : $this->fail();

    }

}
