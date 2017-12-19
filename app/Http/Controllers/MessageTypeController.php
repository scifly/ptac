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
    
    protected $messageType;
    
    function __construct(MessageType $messageType) {
    
        $this->middleware(['auth']);
        $this->messageType = $messageType;
    
    }
    
    /**
     * 消息类型
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->messageType->datatable());
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
        
        return $this->messageType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑消息类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) {
            return $this->notFound();
        }
        
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
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $messageType = $this->messageType->find($id);
        if (!$messageType) { return $this->notFound(); }
        
        return $messageType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
