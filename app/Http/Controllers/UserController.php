<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
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
    
    /**
     * UserController constructor.
     * @param User $user
     * @param Message $message
     * @param Event $event
     */
    function __construct(User $user, Message $message, Event $event) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->message = $message;
        $this->event = $event;
        $this->user = $user;
        
    }
    
    /**
     * 个人信息
     *
     * @throws Throwable
     */
    public function edit() {
        
        return $this->output();

    }
    
    /**
     * 更新用户
     *
     * @param UserRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update(UserRequest $request) {
        
        $user = Auth::user();
        
        return $this->result(
            $user->modify($request->all(), $user->id)
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
    
}
