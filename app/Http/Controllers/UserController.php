<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 用户
 *
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller {
    
    protected $user, $message, $event;
    
    function __construct(User $user, Message $message, Event $event) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->user = $user;
        $this->message = $message;
        $this->event = $event;
        
    }
    
    /**
     * 个人信息
     *
     * @throws Throwable
     */
    public function edit() {
        
        return $this->output([
            'user' => Auth::user()
        ]);

    }
    
    /**
     * 更新用户
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update() {
        
        $user = Auth::user();
        
        return $this->result(
            $user->modify(Request::all(), $user->id)
        );
        
    }
    
    /**
     * 重置密码
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function reset() {
        
        return Request::method() == 'POST'
            ? $this->result($this->user->reset())
            : $this->output();
        
    }
    
    /**
     * 我的消息
     *
     * @throws Throwable
     */
    public function message() {
        
        return Request::get('draw')
            ? response()->json($this->message->index())
            : $this->output();
        
    }
    
    /**
     * 待办事项
     *
     * @throws Throwable
     */
    public function event() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->event->index()
            );
        }
        
        return $this->output();
        
    }
    
}
