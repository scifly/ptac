<?php
namespace App\Http\Controllers\Auth;

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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    /**
     * Create a new controller instance.
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


        $input = $request->input('input');
        $password = $request->input('password');
        if (User::whereUsername($input)->first()) {
            $user = User::whereUsername($input)->first();
            $field = 'username';
        } elseif (User::whereEmail($input)->first()) {
            $user = User::whereEmail($input)->first();
            $field = 'email';
        } else {
            $mobile = Mobile::where('mobile', $input)
                ->where('isdefault', 1)->first();
            if (!$mobile || !$mobile->user_id) {
                return response()->json(['statusCode' => 500]);
            }
            $username = User::whereId($mobile->user_id)->first()->username;
            $user = User::whereUsername($username)->first();
            if (
            Auth::attempt(
                ['username' => $username, 'password' => $password],
                $request->input('remember')
            )
            ) {
                Session::put('user', $user);
                return response()->json([
                    'statusCode' => 200,
                    'url'        => '/',
                ]);
            } else {
                return response()->json(['statusCode' => 500]);
            }
        }
        if (Auth::attempt([$field => $input, 'password' => $password])) {
            Session::put('user', $user);
            return response()->json([
                'statusCode' => 200,
                'url'        => '/',
            ]);
        }
        
        return response()->json(['statusCode' => 500]);
        
    }
    
}
