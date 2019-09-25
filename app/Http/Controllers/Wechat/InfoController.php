<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Throwable;

/**
 * 消息中心
 *
 * Class InfoController
 * @package App\Http\Controllers\Wechat
 */
class InfoController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    protected $info;
    
    /**
     * InfoController constructor.
     * @param Message $info
     */
    public function __construct(Message $info) {
        
        $this->middleware(['corp.auth', 'corp.role'])->except(['detail']);
        $this->info = $info;
        
    }
    
    /**
     * 消息列表
     *
     * @return array|RedirectResponse|Redirector|View|string
     * @throws Throwable
     */
    public function index() {
        
        return $this->info->wIndex();
        
    }
    
    /**
     * 创建消息
     *
     * @return View|string
     * @throws Throwable
     */
    public function create() {

        return $this->info->wCreate();
        
    }
    
    /**
     * 保存消息
     *
     * @param MessageRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        return $this->result(
            $this->info->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 发送消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function send(MessageRequest $request) {
        
        return $this->result(
            $this->info->send(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑消息
     *
     * @param $id
     * @return JsonResponse|View|string
     * @throws Throwable
     */
    public function edit($id = null) {
        
        return $this->info->wEdit($id);
        
    }
    
    /**
     * 更新消息
     *
     * @param MessageRequest $request
     * @param null $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update(MessageRequest $request, $id = null) {
        
        return $this->result(
            $this->info->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 消息详情
     *
     * @param $id
     * @return Factory|View
     * @throws Throwable
     */
    public function show($id = null) {
        
        return $this->info->wShow($id);
        
    }
    
    /**
     * 查看消息
     *
     * @param $code
     * @return Factory|View
     */
    public function detail($code) {
    
        return $this->info->wDetail($code);
    
    }
    
    /**
     * 删除消息
     *
     * @param $id
     * @return bool|JsonResponse|null
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->info->remove($id)
        );
        
    }
    
}