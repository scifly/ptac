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
        // $corpId = 'wxe75227cead6b8aec';
        // $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        // $accessToken = Wechat::getAccessToken($corpId,$secret);
        // $type = 'image';
        // $imgurl = 'http://weixin.028lk.com/img/user3-128x128.jpg';
        // $data = ['media' => curl_file_create($imgurl)]; //PHP>5.5
        // $url = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token= '.$accessToken.'&type='.$type;
        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($curl, CURLOPT_TIMEOUT,60);
        // if (!empty($data)){
        //     curl_setopt($curl, CURLOPT_POST, 1);
        //     curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        //     curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        // }
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // $result = curl_exec($curl);
        // curl_close($curl);
        // print_r($result);
        // die;
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
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receiver(){
    return view('wechat.message_center.add');
    }
}
