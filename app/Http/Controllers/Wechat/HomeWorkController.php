<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\Wechat\JsApiPay;
use App\Helpers\Wechat\WxPayApi;
use App\Helpers\Wechat\WxPayConfig;
use App\Helpers\Wechat\WxPayUnifiedOrder;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Corp;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use SimpleXMLElement;
use Throwable;

/**
 * 布置作业
 *
 * Class HomeWorkController
 * @package App\Http\Controllers\Wechat
 */
class HomeWorkController extends Controller {
    
    static $category = 1; # 微信端控制器
    
    const URL_UNIFIEDORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    protected $hw;
    
    function __construct() {
        
        $this->middleware(['corp.auth', 'corp.role']);
        
    }
    
    /**
     * 微信支付测试
     *
     * @return Factory|View|string
     * @throws Throwable
     */
    public function index() {
    
        try {
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
            $input->setOpenId($this->openId());
            $config = new WxPayConfig();
            $order = WxPayApi::unifiedOrder($config, $input);
            $jsApiPay = new JsApiPay();
            
            return view('wechat.home_work.index', [
                'jsApiParameters' => $jsApiPay->getJsApiParameters($order),
                'editAddress' => $jsApiPay->getEditAddressParameters()
            ]);
        } catch (Exception $e) {
            Log::ERROR($e->getMessage());
        }
    
        return 'something went wrong';
        
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
     * @return Factory|View
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
    
        return view('wechat.home_work.index');
        
    }
    
    /**
     * 获取当前登录用户的openid
     *
     * @return mixed
     * @throws Throwable
     */
    private function openid() {
    
        $corp = Corp::find(session('corpId'));
        $paths = explode('/', Request::path());
        $app = App::whereCorpId($corp->id)->where('name', Constant::APPS[$paths[1]])->first();
        $token = Wechat::getAccessToken($corp->corpid, $app->secret);
        abort_if(
            $token['errcode'],
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            $token['errmsg']
        );
        $user = Auth::user();
        $result = json_decode(
            Wechat::convertToOpenid($token['access_token'], $user->userid), true
        );
        abort_if(
            $result['errcode'],
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Constant::WXERR[$result['errcode']]
        );
        
        return $result['openid'];
        
    }
    
}
