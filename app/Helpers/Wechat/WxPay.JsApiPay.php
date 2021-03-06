<?php
namespace App\Helpers\Wechat;
use App\Facades\Wechat;
use App\Helpers\Constant;
use Exception;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * example目录下为简单的支付样例，仅能用于搭建快速体验微信支付使用
 * 样例的作用仅限于指导如何使用sdk，在安全上面仅做了简单处理， 复制使用样例代码时请慎重
 * 请勿直接直接使用样例对外提供服务
 *
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 *
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 *
 * @author widy
 *
 */
class JsApiPay {
    
    /**
     * 网页授权接口微信服务器返回的数据，返回样例如下
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE",
     *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 其中access_token可用于获取共享收货地址
     * openid是微信支付jsapi支付接口必须的参数
     * @var array
     */
    public $data = null;
    protected $curl_timeout = 360;
    
    /**
     *
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return string openid
     * @throws Throwable
     */
    public function getOpenId() {
        
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            // $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']);
            // $url = $this->_createOauthUrlForCode($baseUrl);
            $url = Wechat::code('wwefd1c6553e218347', '1000002', Request::url());
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            
            return $openid;
        }
    }
    
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return string openid
     * @throws Throwable
     */
    public function getOpenidFromMp($code) {
    
        try {
            $token = Wechat::token(
                'ent',
                'wwefd1c6553e218347',
                'EfS77mm40eYEJgLVJSeuWQgx0odW2vumk2rOxSBvnvg'
            );
            $result = json_decode(
                Wechat::invoke(
                    'ent', 'user',
                    'getuserinfo', [$token, $code]
                ), JSON_UNESCAPED_UNICODE
            );
            throw_if(
                $result['errcode'],
                new Exception(Constant::WXERR[$result['errcode']])
            );
            $result = json_decode(
                Wechat::invoke(
                    'ent', 'user', 'convert_to_openid',
                    [$token], ['userid' => $result['UserId']]
                ), true
            );
            throw_if(
                $result['errcode'],
                new Exception(Constant::WXERR[$result['errcode']])
            );
            return $result['openid'];
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 获取jsapi支付参数
     *
     * @param array $unifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     * @return string - json数据，可直接填入js函数作为参数
     */
    public function getJsApiParameters($unifiedOrderResult) {
        
        if (!array_key_exists("appid", $unifiedOrderResult)
            || !array_key_exists("prepay_id", $unifiedOrderResult)
            || $unifiedOrderResult['prepay_id'] == "") {
            throw new WxPayException("参数错误");
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->setAppid($unifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->setTimeStamp("$timeStamp");
        $jsapi->setNonceStr(WxPayApi::getNonceStr());
        $jsapi->setPackage("prepay_id=" . $unifiedOrderResult['prepay_id']);
        $config = new WxPayConfig();
        $jsapi->setPaySign($jsapi->makeSign($config));
        $parameters = json_encode($jsapi->getValues());
        
        return $parameters;
        
    }
    
    /**
     *
     * 获取地址js参数
     *
     * @return string - 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function getEditAddressParameters() {
        
        $config = new WxPayConfig();
        $getData = $this->data;
        $data = [];
        $data["appid"] = $config->getAppId();
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = WxPayApi::getNonceStr();
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->toUrlParams($data);
        $addrSign = sha1($params);
        $afterData = [
            "addrSign"  => $addrSign,
            "signType"  => "sha1",
            "scope"     => "jsapi_address",
            "appId"     => $config->getAppId(),
            "timeStamp" => $data["timestamp"],
            "nonceStr"  => $data["noncestr"],
        ];
        $parameters = json_encode($afterData);
        
        return $parameters;
        
    }
    
    /**
     * 拼接签名字符串
     *
     * @param array $urlObj
     * @return string - 返回已经拼接好的字符串
     */
    private function toUrlParams($urlObj) {
        
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        
        return $buff;
        
    }
    
}
