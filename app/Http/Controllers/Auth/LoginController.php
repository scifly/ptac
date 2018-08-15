<?php
namespace App\Http\Controllers\Auth;

use App\Helpers\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Mobile;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
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
        if ($user = User::whereUsername($input)->first()) {
            $field = 'username';
            # 邮箱登录
        } elseif ($user = User::whereEmail($input)->first()) {
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
            $user = $mobile->user;
            # 通过默认手机号码查询对应的用户名
            $field = 'username';
            $input = User::find($mobile->user_id)->username;
        }
        # 角色为监护人/学生/api的用户不得登录后台
        abort_if(
            ($user->custodian && !$user->educator) || $user->student || $user->group->name == 'api',
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.invalid_credentials')
        );
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
    
}
