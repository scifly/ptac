<?php
namespace App\Http\Controllers\Auth;

use App\Helpers\HttpStatusCode;
use App\Helpers\Wechat\WXBizMsgCrypt;
use App\Http\Controllers\Controller;
use App\Models\Corp;
use App\Models\Mobile;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
    
    /**
     * 登录后的跳转地址
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    /**
     * 创建控制器实例
     *
     */
    public function __construct() {
        
        $this->middleware('guest')->except('logout');
        
    }
    
    /**
     * 登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        
        if (!$request->ajax() && Auth::id()) {
            return response()->redirectTo($request->server('HTTP_REFERER'));
        }
        $returnUrl = null;
        if ($request->get('returnUrl')) {
            $returnUrl = urldecode($request->get('returnUrl'));
        }
        if (Auth::id()) {
            $this->result['url'] = $returnUrl ? $returnUrl : '/';
            
            return response()->json($this->result);
        }
        $input = $request->input('input');
        $password = $request->input('password');
        $rememberMe = $request->input('rememberMe') == 'true' ? true : false;
        # 用户名登录
        if (User::whereUsername($input)->first()) {
            $field = 'username';
            # 邮箱登录
        } elseif (User::whereEmail($input)->first()) {
            $field = 'email';
            # 手机号码登录
        } else {
            # 获取用户的默认手机号码
            $mobile = Mobile::whereMobile($input)->where('isdefault', 1)->first();
            abort_if(
                !$mobile || !$mobile->user_id,
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.invalid_credentials')
            );
            # 通过默认手机号码查询对应的用户名
            $field = 'username';
            $input = User::find($mobile->user_id)->username;
        }
        # 登录(用户名或邮箱)
        if (Auth::attempt(
            [$field => $input, 'password' => $password],
            $rememberMe
        )) {
            $this->result['url'] = $returnUrl ? $returnUrl : '/';
            
            return response()->json($this->result);
        }
        
        return abort(
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.invalid_credentials')
        );
        
    }
    
    /**
     * 接收通讯录变更事件
     * @param Request $request
     */
    public function sync(Request $request) {
        
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
