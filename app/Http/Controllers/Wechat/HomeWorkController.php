<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\Wechat\JsApiPay;
use App\Helpers\Wechat\WxPayApi;
use App\Helpers\Wechat\WxPayConfig;
use App\Helpers\Wechat\WxPayUnifiedOrder;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use SimpleXMLElement;

/**
 * Class HomeWorkController
 * @package App\Http\Controllers\Wechat
 */
class HomeWorkController extends Controller {
    
    const URL_UNIFIEDORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

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
    
        try {
            $tools = new JsApiPay();
            $openId = $tools->getOpenId();
            $input = new WxPayUnifiedOrder();
            $input->setBody("test");
            $input->setAttach("test");
            $input->setOutTradeNo("sdkphp" . date("YmdHis"));
            $input->setTotalFee("1");
            $input->setTimeStart(date("YmdHis"));
            $input->setTimeExpire(date("YmdHis", time() + 600));
            $input->setGoodsTag("test");
            $input->setNotifyUrl("http://paysdk.weixin.qq.com/notify.php");
            $input->setTradeType("JSAPI");
            $input->setOpenId($openId);
            Log::debug(json_encode($input));
            $config = new WxPayConfig();
            $order = WxPayApi::unifiedOrder($config, $input);
    
            return view('wechat.homework.index', [
                'jsApiParameters' => $tools->getJsApiParameters($order),
                'editAddress' => $tools->getEditAddressParameters()
            ]);
        } catch (Exception $e) {
            Log::ERROR(json_encode($e->getMessage()));
        }
    
        return 'something went wrong';
        
    }
    
    public function notify() {
        
        echo 'whatsoever';
        
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
            curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
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
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws Exception
     */
    private function html5pay() {
    
        // return $this->hw->wIndex();
        if (Request::method() == 'POST') {
            $apiKey = '4372983891jkfdl43u2okjdkkdkfjkkk';
            $nonce = $this->randomstring(32);
            $ip = Request::ip();
            $params = [
                'appid' => 'wwefd1c6553e218347',
                'body' => 'english',
                'mch_id' => '1226652702',
                'nonce_str' => $nonce,
                'notify_url' => 'http://weixin.028lk.com/wlrj/notify',
                'out_trade_no' => '1415659990',
                'scene_info' => '{"h5_info": {"type":"Wap","wap_url":"http://weixin.028lk.com/wlrj/homework","wap_name":"english"}}',
                'spbill_create_ip' => $ip,
                'total_fee' => '1',
                'trade_type' => 'MWEB',
            ];
            $str = '';
            ksort($params);
            foreach ($params as $key => $value) {
                $str .= $key . '=' . $value . '&';
            }
            $strTemp = $str . 'key=' . $apiKey;
            $sign = strtoupper(md5($strTemp));
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
    
}
