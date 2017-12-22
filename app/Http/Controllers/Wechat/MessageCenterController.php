<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Support\Facades\Request;

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
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $agentId = 3;
        $code = Request::input('code');
        $accessToken = Wechat::getAccessToken($corpId,$secret);
        if (empty($code)) {
            $codeUrl = Wechat::getCodeUrl($corpId,$agentId,'http://weixin.028lk.com/message_center');
            return redirect($codeUrl);
        } else {
            $code = Request::get('code');
            print_r($code . '<br/>');
            die;
            // $accessToken = Wechat::getAccessToken($corpId,$secret);
            // print_r($accessToken);
            // $userInfo = Wechat::getUserInfo($accessToken,$code);
            // echo '<pre>';
            // print_r($userInfo);
        }
    
        // return view('wechat.message_center.index');
    }
}
