<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageTypeRequest;
use App\Models\MessageType;
use Illuminate\Support\Facades\Request;

class MessageTypeController extends Controller {
    
    protected $messageType;
    
    function __construct(MessageType $messageType) { $this->messageType = $messageType; }
    
    /**
     * 显示消息类型列表
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
     * 显示创建消息类型记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的消息类型记录
     *
     * @param MessageTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MessageTypeRequest $request) {
        
        return $this->messageType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的消息类型记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['messageType' => $messageType]);
        
    }
    
    /**
     * 显示编辑指定消息类型记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $messageType = $this->messageType->find($id);
        if (!$messageType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['messageType' => $messageType]);
    
    }
    
    /**
     * 更新指定的消息类型记录
     *
     * @param MessageTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MessageTypeRequest $request, $id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) { return $this->notFound(); }
        return $messageType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的消息类型记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) { return $this->notFound(); }
        return $messageType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
