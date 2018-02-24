<?php
namespace App\Http\Controllers\Auth;

use App\Helpers\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Mobile;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
            return response()->json([
                'statusCode' => HttpStatusCode::OK,
                'url' => $returnUrl ? $returnUrl : '/'
            ]);
        }
        $input = $request->input('input');
        $password = $request->input('password');
        $rememberMe = $request->input('rememberMe') == 'true' ? true : false;
        # 用户名登录
        if (User::whereUsername($input)->first()) {
            $user = User::whereUsername($input)->first();
            $field = 'username';
        # 邮箱登录
        } elseif (User::whereEmail($input)->first()) {
            $user = User::whereEmail($input)->first();
            $field = 'email';
        # 手机号码登录
        } else {
            # 获取用户的默认手机号码
            $mobile = Mobile::whereMobile($input)
                ->where('isdefault', 1)->first();
            if (!$mobile || !$mobile->user_id) {
                return response()->json([
                    'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR
                ]);
            }
            # 通过默认手机号码查询对应的用户名
            $username = User::find($mobile->user_id)->username;
            $user = User::whereUsername($username)->first();
            # 通过用户名登录
            if (Auth::attempt(
                ['username' => $username, 'password' => $password],
                $rememberMe
            )) {
                Session::put('user', $user);
                return response()->json([
                    'statusCode' => HttpStatusCode::OK,
                    'url'        => $returnUrl ? $returnUrl : '/',
                ]);
            } else {
                return response()->json([
                    'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR
                ]);
            }
        }
        # 登录(用户名或邮箱)
        if (Auth::attempt(
            [$field => $input, 'password' => $password],
            $rememberMe
        )) {
            Session::put('user', $user);
            return response()->json([
                'statusCode' => HttpStatusCode::OK,
                'url'        => $returnUrl ? $returnUrl : '/',
            ]);
        }
        
        return response()->json([
            'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR
        ]);
        
    }
    
}
