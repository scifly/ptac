<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Request;
use SimpleXMLElement;

/**
 * Class HomeWorkController
 * @package App\Http\Controllers\Wechat
 */
class HomeWorkController extends Controller {
    
    const URL_UNIFIEDORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
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
        
        // $this->middleware('wechat');
        
    }
    
    /**
     * 微信支付测试
     *
     * @throws Exception
     */
    public function index() {
        
        if (Request::method() == 'POST') {
            $apiKey = '43728910dsajfksfdjksalj432443AAA';
            // return $this->hw->wIndex();
            $params = [
                'appid' => 'wwefd1c6553e218347',
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
            $params = array_flip($params);
            $xml = new SimpleXMLElement('<xml/>');
            array_walk_recursive($params, [$xml, 'addChild']);
            $strXml = preg_replace('/^.+\n/', '', $xml->asXML());
    
            $result = simplexml_load_string(
                $this->curlPost(self::URL_UNIFIEDORDER, $strXml),
                'SimpleXMLElement', LIBXML_NOCDATA
            );
    
            dd($result);
        }

        return view('wechat.homework.index');
        
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
    
    /**
     * 发送POST请求
     *
     * @param $url
     * @param mixed $formData
     * @return mixed|null
     * @throws Exception
     */
    private function curlPost($url, $formData) {
        
        $result = null;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            // Check the return value of curl_exec(), too
            if (!$result) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
        
    }
    
}
