<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Throwable;

/**
 * Class MessageCenterController
 * @package App\Http\Controllers\Wechat
 */
class MessageCenterController extends Controller {
    
    protected $message;
    
    /**
     * MessageCenterController constructor.
     * @param Message $message
     */
    public function __construct(Message $message) {
        
        $this->middleware('wechat');
        $this->message = $message;
        
    }
    
    /**
     * 消息列表
     *
     * @return array|RedirectResponse|Redirector|View|string
     * @throws Throwable
     */
    public function index() {
        
        return $this->message->wIndex();
        
    }
    
    /**
     * 创建消息
     *
     * @return View|string
     * @throws Throwable
     */
    public function create() {

        return $this->message->wCreate();
        
    }
    
    /**
     * 保存消息（草稿）
     *
     * @param MessageRequest $request
     * @return JsonResponse
     */
    public function store(MessageRequest $request) {
        
        return $this->result(
            $this->message->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 保存并发送消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function send(MessageRequest $request) {
        
        return $this->result(
            $this->message->send(
                $request->all()
            )
        );
        
    }
    
    /**
     * 消息编辑页面
     *
     * @param $id
     * @return JsonResponse|View|string
     * @throws Throwable
     */
    public function edit($id = null) {
        
        return $this->message->wEdit($id);
        
    }
    
    /**
     * 更新消息草稿
     *
     * @param MessageRequest $request
     * @param null $id
     * @return JsonResponse|string
     * @throws Exception
     */
    public function update(MessageRequest $request, $id = null) {
        
        return $this->result(
            $this->message->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 消息详情页面展示
     *
     * @param $id
     * @return Factory|View
     * @throws Throwable
     */
    public function show($id = null) {
        
        return $this->message->wShow($id);
        
    }
    
    /**
     * 删除指定消息
     *
     * @param $id
     * @return bool|JsonResponse|null
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->message->remove($id)
        );
        
    }
    
}