<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
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
        
        if (Request::get('draw')) {
            return response()->json(
                $this->order->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 保存订单
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request) {
        
        return $this->result(
            $this->order->create($request->all())
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
        
        $order = $this->order->find($id);
        abort_if(!$order, HttpStatusCode::NOT_FOUND);
        
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
        
        $order = $this->order->find($id);
        abort_if(!$order, HttpStatusCode::NOT_FOUND);
        
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
        
        $order = $this->order->find($id);
        abort_if(!$order, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $order->delete()
        );
        
    }
    
}
