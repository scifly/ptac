<?php
namespace App\Http\Controllers;

use App\Http\Requests\MessageTypeRequest;
use App\Models\MessageType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 消息类型
 *
 * Class MessageTypeController
 * @package App\Http\Controllers
 */
class MessageTypeController extends Controller {
    
    protected $mt;
    
    /**
     * MessageTypeController constructor.
     * @param MessageType $mt
     */
    function __construct(MessageType $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
        $this->approve($mt);
        
    }
    
    /**
     * 消息类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->mt->index())
            : $this->output();
        
    }
    
    /**
     * 创建消息类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
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
        
        return $this->result(
            $this->mt->store($request->all())
        );
        
    }
    
    /**
     * 编辑消息类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'mt' => MessageType::find($id),
        ]);
        
    }
    
    /**
     * 更新消息类型
     *
     * @param MessageTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(MessageTypeRequest $request, $id) {
        
        return $this->result(
            $this->mt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->mt->remove($id)
        );
        
    }
    
}
