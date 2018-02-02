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
    
    protected $mt;
    
    function __construct(MessageType $mt) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
    
    }
    
    /**
     * 消息类型
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->mt->datatable()
            );
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
        
        return $this->result(
            $this->mt->store($request->all())
        );
        
    }
    
    /**
     * 编辑消息类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $mt = MessageType::find($id);
        abort_if(!$mt, self::NOT_FOUND);
        
        return $this->output([
            'mt' => $mt
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
        
        $mt = MessageType::find($id);
        abort_if(!$mt, self::NOT_FOUND);
        
        return $this->result(
            $mt->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $mt = MessageType::find($id);
        abort_if(!$mt, self::NOT_FOUND);
        
        return $this->result(
            $mt->remove($id)
        );
        
    }
    
}
