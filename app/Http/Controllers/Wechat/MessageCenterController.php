<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;

class MessageCenterController extends Controller {
    
    protected $message;
    
    /**
     * MessageCenterController constructor.
     * @param Message $message
     */
    public function __construct(Message $message) {
        //判断角色
        // $this->middleware();
        $this->message = $message;
        
    }
    
    /**
     * @return string
     */
    public function index() {

        //获取微信用户信息

        // $corpId = 'wxe75227cead6b8aec';
        // $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        // $agentId = 3;
        // $code = Request::input('code');
        // if (empty($code)) {
        //     $codeUrl = Wechat::getCodeUrl($corpId,$agentId,'http://weixin.028lk.com/message_center');
        //     return redirect($codeUrl);
        // } else {
        //     $code = Request::get('code');
        //     $accessToken = Wechat::getAccessToken($corpId,$secret);

        //     $userInfo = json_decode(Wechat::getUserInfo($accessToken,$code),JSON_UNESCAPED_UNICODE);
        //     $userId = $userInfo['UserId'];
        //     $user = User::whereUserid($userId)->first();
        //     Auth::loginUsingId($userId);
        // }
        $userId = 'kobe';
        $user = User::whereUserid($userId)->first();
        $role = $user->group->name;
        if($role == '教职员工'){
            $receiveMessages = $sendMessages = [];
            $receiveMessages = $this->message->where('r_user_id',$user->id)->get();
            $sendMessages = $this->message->where('s_user_id',$user->id)->get();
            return view('wechat.message_center.index',[
                'receiveMessages' => $receiveMessages,
                'sendMessages' => $sendMessages
            ]);
        }

    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
     
        return view('wechat.message_center.create');
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show() {
     
        return view('wechat.message_center.show');

    }
}
