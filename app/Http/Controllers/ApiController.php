<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Jobs\SendMessageApi;
use App\Models\{Consumption, Group, Message, User};
use Exception;
use Illuminate\{Http\JsonResponse, Support\Facades\Auth, Support\Facades\Request};
use Throwable;

/**
 * 接口
 *
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {
    
    protected $consumption, $message, $reaction;
    
    /**
     * ApiController constructor.
     * @param Consumption $consumption
     * @param Message $message
     */
    function __construct(
        Consumption $consumption,
        Message $message
    ) {
        
        $this->consumption = $consumption;
        $this->message = $message;
        $this->reaction = [
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.ok')
        ];
        
    }
    
    /**
     * 登录
     *
     * @return JsonResponse
     */
    public function signin() {
    
        $username = request('username');
        $credential = [
            'username' => $username,
            'password' => request('password')
        ];
        $apiUser = User::where([
            'username' => $username,
            'group_id' => Group::whereName('api')->first()->id
        ])->first();
        if ($apiUser && (Auth::id() || Auth::attempt($credential))) {
            $user = Auth::user();
            $this->reaction['token'] = $user->createToken('ptac')->accessToken;
        } else {
            $this->reaction['message'] = __('messages.forbidden');
            $this->reaction['statusCode'] = HttpStatusCode::UNAUTHORIZED;
        }
        
        return response()->json(
            $this->reaction,
            $this->reaction['statusCode']
        );
        
    }
    
    /**
     * 消费记录
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function studentConsumption() {

        return $this->consumption->store();
        
    }
    
    /**
     * 发送消息（短信或者微信）
     *
     * @return string
     * @throws Exception
     */
    public function sendMsg() {
    
        SendMessageApi::dispatch(
            Request::input('mobiles'),
            Request::input('school_id'),
            Request::input('content'),
            Auth::user()
        );
        
        return response()->json([
            'statusCode' => HttpStatusCode::OK,
            'message' => '已成功提交消息发送请求'
        ]);
    
    }
    
}
