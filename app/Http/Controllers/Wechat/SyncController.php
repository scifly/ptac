<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\Wechat\WXBizMsgCrypt;
use App\Http\Controllers\Controller;
use App\Models\Corp;
use App\Models\Mobile;
use App\Models\User;
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
     * @throws \Exception
     */
    public function sync() {
        
        $this->verifyUrl();
        exit;
        
        $paths = explode('/', Request::path());
        $corp = Corp::whereAcronym($paths[0])->first();
        // 假设企业号在公众平台上设置的参数如下
        $wxcpt = new WXBizMsgCrypt(
            $corp->token,
            $corp->encoding_aes_key,
            $corp->corpid
        );
        $msgSignature = Request::query('msg_signature');
        $timestamp = Request::query('timestamp');
        $nonce = Request::query('nonce');
        $content = '';
        $errcode = $wxcpt->DecryptMsg(
            $msgSignature,
            $timestamp,
            $nonce,
            Request::getContent(),
            $content
        );
        if ($errcode) { return $errcode; }
        $event = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        $userid = $event->{'UserID'};
        Log::debug(json_encode($event));
        $user = User::whereUserid($userid)->first();
        $token = Wechat::getAccessToken(
            $corp->corpid,
            $corp->contact_sync_secret,
            true
        );
        if ($token['errcode']) { return $token['errcode']; }
        $member = json_decode(Wechat::getUser($token['access_token'], $userid));
        if ($member->{'errcode'}) {
            Log::debug(json_encode($member));
            return $member->{'errcode'};
        }
        $type = $event->{'Event'};
        switch ($type) {
            case 'subscribe':
                $user->update([
                    'avatar_url' => $member->{'avatar'},
                    'subscribed' => 1,
                ]);
                break;
            case 'unsubscribe':
                $user->update(['subscribed' => 0]);
                break;
            case 'change_contact':
                $data = [
                    'gender'     => property_exists($member, 'gender')
                        ? ($member->{'gender'} == 1 ? 0 : 1)
                        : $user->gender,
                    'email'      => property_exists($member, 'email')
                        ? $member->{'email'}
                        : $user->email,
                    'avatar_url' => property_exists($member, 'avatar')
                        ? $member->{'avatar'}
                        : $user->avatar_url,
                    'subscribed' => property_exists($member, 'status')
                        ? ($member->{'status'} == 1 ? 1 : 0)
                        : $user->subscribed,
                ];
                Log::debug(json_encode($data));
                $user->update($data);
                if (property_exists($member, 'mobile')) {
                    Mobile::whereUserId($user->id)->where('isdefault', 1)->first()->update([
                        'mobile' => $member->{'mobile'},
                    ]);
                }
                break;
            default:
                break;
        }
        
        return '';
        
    }
    
    public function verifyUrl() {
        
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
        echo !$errCode ? $sEchoStr : "ERR: " . $errCode . "\n\n";
        
    }
    
}