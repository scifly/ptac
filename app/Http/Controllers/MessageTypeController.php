<?php
namespace App\Http\Controllers;

use App\Http\Requests\MessageTypeRequest;
use App\Models\MessageType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 消息类型
 *
 * Class MessageTypeController
 * @package App\Http\Controllers
 */
class MessageTypeController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
    
    }
    
    /**
     * 消息类型
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(MessageType::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建消息类型
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存消息类型
     *
     * @param MessageTypeRequest $request
     * @return JsonResponse
     */
    public function store(MessageTypeRequest $request) {
        
        return $this->result(MessageType::create($request->all()));
        
    }
    
    /**
     * 编辑消息类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $messageType = MessageType::find($id);
        if (!$messageType) { return $this->notFound(); }
        
        return $this->output(['messageType' => $messageType]);
        
    }
    
    /**
     * 更新消息类型
     *
     * @param MessageTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(MessageTypeRequest $request, $id) {
        
        $messageType = MessageType::find($id);
        if (!$messageType) { return $this->notFound(); }
        
        return $this->result($messageType->update($request->all()));
        
    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $messageType = MessageType::find($id);
        if (!$messageType) { return $this->notFound(); }
        
        return $this->result($messageType->delete());
        
    }
    
}
