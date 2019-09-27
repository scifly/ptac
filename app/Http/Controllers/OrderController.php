<?php
namespace App\Http\Controllers;

use App\Helpers\Constant;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Exception;
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
        $this->order = $order;
        
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
        
        abort_if(
            !($order = $this->order->find($id)),
            Constant::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $this->output([
            'order' => $order,
        ]);
        
    }
    
    /**
     * 更新订单
     *
     * @param OrderRequest $request
     * @param $id
     * @return bool|JsonResponse
     */
    public function update(OrderRequest $request, $id) {
        
        abort_if(
            !($order = $this->order->find($id)),
            Constant::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $order->update(
            $request->all()
        );
        
    }
    
    /**
     * 删除订单
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        abort_if(
            !($order = $this->order->find($id)),
            Constant::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $this->result(
            $order->delete()
        );
        
    }
    
}
