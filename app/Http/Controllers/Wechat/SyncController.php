<?php
namespace App\Http\Controllers\Wechat;

use App\Models\Corp;
use App\Http\Controllers\Controller;
use App\Helpers\Wechat\WXBizMsgCrypt;
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
        
        Log::debug('4378219047389012');
        // $this->verifyUrl();
        Log::debug(json_encode(Request::getContent()));
        
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