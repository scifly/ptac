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
        $imgurl = 'http://weixin.028lk.com/img/user3-128x128.jpg';
        $data=array('media'=>curl_file_create($imgurl)); //PHP>5.5
        $result = Wechat::curlPost($imgurl,$data);
        print_r($result);
        die;
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
        //     $userInfo = Wechat::getUserInfo($accessToken,$code);
        //     //{
        //     //"UserId":"yuanhongbin",
        //     //"DeviceId":"873fbe89aec047758ebbc6b41e4b45f9",
        //     //"errcode":0,
        //     //"errmsg":"ok",
        //     //"user_ticket":"xVAunChCFmwvi1yJokTRDtzOuIyyvJuYQ2Q59LjiUB-b1O9rJZlx0pGXrNPGjGv7h13abTLvn30oxT-BqvY7YQ",
        //     //"expires_in":1800
        //     //}
        // }
    
        return view('wechat.message_center.index');
    }
}
