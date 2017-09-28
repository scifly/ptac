<?php
namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Request;

/**
 * 订单
 *
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController extends Controller {

    protected $order;

    function __construct(Order $order) {

        $this->order = $order;

    }

    /**
     * 订单列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->order->datatable());
        }

        return $this->output(__METHOD__);

    }

    /**
     * 保存订单
     *
     * @param OrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request) {

        return $this->order->create($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 订单详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $order = $this->order->find($id);
        if (!$order) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, ['order' => $order]);

    }

    /**
     * 更新订单
     *
     * @param OrderRequest $request
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(OrderRequest $request, $id) {

        $order = $this->order->find($id);
        if (!$order) {
            return $this->notFound();
        }

        return $order->update($request->all());

    }

    /**
     * 删除订单
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $order = $this->order->find($id);
        if (!$order) {
            return $this->notFound();
        }

        return $order->delete() ? $this->succeed() : $this->fail();

    }

}
