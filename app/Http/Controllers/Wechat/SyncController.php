<?php
namespace App\Http\Controllers\Wechat;

use App\Events\JobResponse;
use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use App\Http\Controllers\Controller;
use App\Helpers\Wechat\WXBizMsgCrypt;
use App\Models\Mobile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * 微信考勤
 *
 * Class AttendanceController
 * @package App\Http\Controllers\Wechat
 */
class SyncController extends Controller {
    
    function __construct() { }
    
    /**
     * 接收通讯录变更事件
     */
    public function sync() {
    
        $paths = explode('/', Request::path());
        $corp = Corp::whereAcronym($paths[0])->first();
    
        // 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = $corp->encoding_aes_key;
        $token = $corp->token;
        $corpId = $corp->corpid;
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        
        $msgSignature = Request::query('msg_signature');
        $timestamp = Request::query('timestamp');
        $nonce = Request::query('nonce');
        
        $content = '';
        $errcode = $wxcpt->DecryptMsg($msgSignature, $timestamp, $nonce, Request::getContent(), $content);
        if ($errcode) { return false; }
        $event = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        $userid = $event->{'FromUserName'};
        $user = User::whereUserid($userid)->first();
        $member = json_decode(Wechat::getUser($token['access_token'], $userid));
        $type = $event->{'Event'};
        switch ($type) {
            case 'subscribe':
                $user->update([
                    'avatar_url' => $member->{'avatar'},
                    'subscribed' => 1
                ]);
                break;
            case 'unsubscribe':
                $user->update(['subscribed' => 0]);
                break;
            case 'change_contact':
                $data = [
                    'gender' => property_exists($member, 'Gender') ? ($member->{'Gender'} == 1 ? 0 : 1) : $user->gender,
                    'email' => property_exists($member, 'Email') ? $member->{'Email'} : $user->email,
                    'avatar_url' => property_exists($member, 'Avatar') ? $member->{'Avatar'} : $user->avatar_url,
                    'subscribed' => property_exists($member, 'Status') ? ($member->{'Status'} == 1 ? 1 : 0) : $user->subscribed
                ];
                $user->update($data);
                if (property_exists($member, 'Mobile')) {
                    Mobile::whereUserId($user->id)->where('isdefault', 1)->first()->update([
                        'mobile' => $member->{'Mobile'}
                    ]);
                }
                break;
            default:
                break;
        }
        
        return true;
        
    }
    
    private function verifyUrl() {
    
        $paths = explode('/', Request::path());
        $corp = Corp::whereAcronym($paths[0])->first();
    
        // 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = $corp->encoding_aes_key;
        $token = $corp->token;
        $corpId = $corp->corpid;
        $sVerifyMsgSig = Request::query('msg_signature');
        $sVerifyTimeStamp = Request::query('timestamp');
        $sVerifyNonce = Request::query('nonce');
        $sVerifyEchoStr = rawurldecode(Request::query('echostr'));
    
        // 需要返回的明文
        $sEchoStr = "";
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $errCode = $wxcpt->VerifyURL(
            $sVerifyMsgSig,
            $sVerifyTimeStamp,
            $sVerifyNonce,
            $sVerifyEchoStr,
            $sEchoStr
        );
        // Log::debug('error: ' . $errCode);
        // Log::debug('sEchoStr: ' . $sEchoStr);
        if ($errCode == 0) {
            // var_dump($sEchoStr);
            echo $sEchoStr;
        } else {
            print("ERR: " . $errCode . "\n\n");
        }
        
    }
    
}