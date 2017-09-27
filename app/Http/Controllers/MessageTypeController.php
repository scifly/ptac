<?php
namespace App\Http\Controllers;

use App\Http\Requests\MessageTypeRequest;
use App\Models\MessageType;
use Illuminate\Support\Facades\Request;

/**
 * 消息类型
 *
 * Class MessageTypeController
 * @package App\Http\Controllers
 */
class MessageTypeController extends Controller {
    
    protected $messageType;
    
    function __construct(MessageType $messageType) { $this->messageType = $messageType; }
    
    /**
     * 消息类型
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->messageType->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建消息类型
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存消息类型
     *
     * @param MessageTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MessageTypeRequest $request) {
        
        return $this->messageType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 消息类型详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['messageType' => $messageType]);
        
    }
    
    /**
     * 编辑消息类型
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['messageType' => $messageType]);
        
    }
    
    /**
     * 更新消息类型
     *
     * @param MessageTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MessageTypeRequest $request, $id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) {
            return $this->notFound();
        }
        return $messageType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) {
            return $this->notFound();
        }
        return $messageType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
