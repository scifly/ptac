<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;

/**
 * Class HomeWorkController
 * @package App\Http\Controllers\Wechat
 */
class HomeWorkController extends Controller {
    
    const PARAM = <<<XML
<xml>
    <appid>wxe75227cead6b8aec</appid>
    <body>H5支付测试</body>
    <mch_id>1226652702</mch_id>
    <nonce_str>%s</nonce_str>
    <notify_url>http://weixin.028lk.com/wlrj/notify</notify_url>
    <out_trade_no>1415659990</out_trade_no>
    <spbill_create_ip>%s</spbill_create_ip>
    <total_fee>1</total_fee>
    <trade_type>MWEB</trade_type>
    <scene_info>{"h5_info": {"type":"Wap","wap_url":"http://weixin.028lk.com/wlrj/homework","wap_name":"一卡通充值"}}</scene_info>
    <sign>%s</sign>
</xml>
XML;
    protected $hw;
    
    function __construct() {
        
        $this->middleware('wechat');
        
    }
    
    public function index() {
        
        $apiKey = '43728910dsajfksfdjksalj432443AAA';
        // return $this->hw->wIndex();
        $params = [
            'appid' => 'wxe75227cead6b8aec',
            'body' => 'H5支付测试',
            'mch_id' => '1226652702',
            'nonce_str' => $this->randomstring(32),
            'notify_url' => 'http://weixin.028lk.com/wlrj/notify',
            'out_trade_no' => '1415659990',
            'spbill_create_ip' => Request::ip(),
            'total_fee' => 1,
            'trade_type' => 'MWEB',
            'scene_info' => '{"h5_info": {"type":"Wap","wap_url":"http://weixin.028lk.com/wlrj/homework","wap_name":"一卡通充值"}}',
        ];
        $strTemp = http_build_query($params) . '&key=' . $apiKey;
        $sign = strtoupper(hash_hmac('sha256', strtoupper(md5($strTemp)), $apiKey));
        $params['sign'] = $sign;
        
        
    }
    
    /**
     * 生成碎金字符串
     *
     * @param $len
     * @return string
     */
    private function randomstring($len) {
        
        $string = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $len; $i++)
            $string .= substr($chars, rand(0, strlen($chars)), 1);
        
        return $string;
        
    }
    
}
