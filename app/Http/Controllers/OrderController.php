<?php
namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 订单
 *
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController extends Controller {
    
    protected $order;
    
    /**
     * OrderController constructor.
     * @param Order $order
     */
    function __construct(Order $order) {
        
        $this->middleware(['auth']);
        $this->approve($this->order = $order);
        
    }
    
    /**
     * 订单列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->order->index())
            : $this->output();
        
    }
    
    /**
     * 保存订单
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request) {
        
        return $this->result(
            $this->order->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 订单详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->output([
            'order' => $this->order->find($id),
        ]);
        
    }
    
    /**
     * 更新订单
     *
     * @param OrderRequest $request
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function update(OrderRequest $request, $id) {
        
        return $this->order->modify(
            $request->all(), $id
        );
        
    }
    
    /**
     * 删除订单
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->order->remove($id)
        );
        
    }
    
}
