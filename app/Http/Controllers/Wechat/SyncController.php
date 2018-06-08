<?php
namespace App\Http\Controllers\Wechat;

use App\Models\Corp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Wechat\WXBizMsgCrypt;
use Illuminate\Support\Facades\Log;

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
     * @param Request $request
     */
    public function sync(Request $request) {
        
        Log::debug('wtf');
        $paths = explode('/', $request->path());
        $corp = Corp::whereAcronym($paths[0])->first();
        
        // 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = $corp->encoding_aes_key;
        $token = $corp->token;
        $corpId = $corp->corpid;
        $sVerifyMsgSig = $request->query('msg_signature');
        $sVerifyTimeStamp = $request->query('timestamp');
        $sVerifyNonce = $request->query('nonce');
        $sVerifyEchoStr = urldecode($request->query('echostr'));
        
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
        if ($errCode == 0) {
            var_dump($sEchoStr);
        } else {
            print("ERR: " . $errCode . "\n\n");
        }
        
    }
    
}