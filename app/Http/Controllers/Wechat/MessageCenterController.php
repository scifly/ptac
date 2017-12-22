<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessageCenterController extends Controller
{

    /**
     * @return string
     */
    public function index() {

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
        $userId = 'educator_5a2f991358ca9';
        $user = User::whereUserid($userId)->first();
        $role = $user->group->name;
        if($role == '教职员工'){
            $sendMessage = Message::whereMslId($userId)->get();
            return view('wechat.message_center.index');
        }
    }

}
