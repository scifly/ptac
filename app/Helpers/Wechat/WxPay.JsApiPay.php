<?php
namespace App\Helpers\Wechat;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

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
     *
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
     */
    public function getOpenId() {
        
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            // $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']);
            // $url = $this->_createOauthUrlForCode($baseUrl);
            $url = Wechat::getCodeUrl('wwefd1c6553e218347', '1000002', Request::url());
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
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return string - 返回构造好的url
     */
    private function _createOauthUrlForCode($redirectUrl) {
        
        $config = new WxPayConfig();
        $urlObj["appid"] = $config->getAppId();
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->toUrlParams($urlObj);
        
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
        
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
    
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return string openid
     */
    public function getOpenidFromMp($code) {
        
        // $url = $this->__createOauthUrlForOpenid($code);
        $token = Wechat::getAccessToken(
            'wwefd1c6553e218347',
            'EfS77mm40eYEJgLVJSeuWQgx0odW2vumk2rOxSBvnvg'
        );
        if ($token['errcode']) {
            abort(
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                $token['errmsg']
            );
        }
        $accessToken = $token['access_token'];
        $result = json_decode(
            Wechat::getUserInfo($accessToken, $code),
            JSON_UNESCAPED_UNICODE
        );
        abort_if(
            $result['errcode'],
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Constant::WXERR[$result['errcode']]
        );
        $result = json_decode(
            Wechat::convertToOpenid($accessToken, $result['UserId'], '1000002')
        );
        Log::debug(json_encode($result));
        exit;
        // //初始化curl
        // $ch = curl_init();
        // $curlVersion = curl_version();
        // $config = new WxPayConfig();
        // $ua = "WXPaySDK/3.0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlVersion['version'] . " "
        //     . $config->getMerchantId();
        // //设置超时
        // curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        // curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // $proxyHost = "0.0.0.0";
        // $proxyPort = 0;
        // $config->getProxy($proxyHost, $proxyPort);
        // if ($proxyHost != "0.0.0.0" && $proxyPort != 0) {
        //     curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
        //     curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        // }
        // //运行curl，结果以jason形式返回
        // $res = curl_exec($ch);
        // Log::debug(json_encode($res));
        // curl_close($ch);
        // //取出openid
        // $data = json_decode($res, true);
        // $this->data = $data;
        // $openid = $data['openid'];
        
        return $data['openid'];
        
    }
    
    /**
     *
     * 构造获取open和access_toke的url地址
     * @param $code
     * @return string - 请求的url
     */
    private function __createOauthUrlForOpenid($code) {
        
        $config = new WxPayConfig();
        $urlObj["appid"] = $config->getAppId();
        $urlObj["secret"] = $config->getAppSecret();
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->toUrlParams($urlObj);
        
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
        
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
    
}
