<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Mobile;

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
    
    /*public function rules() {
        
        return [
            'login' => 'required',
            'password' => 'required',
        ];
        
    }*/
    
    public function login(Request $request) {
        $input = $request->input('input');
        $password = $request->input('password');
        $field = '';
        if (!(User::whereUsername($input)->first()) && !(User::whereEmail($input)->first())) {
            $mobile = Mobile::whereMobile($input)->where('is_default', 1)->first();
            if (!$mobile->user_id) {
                return response()->json([
                    'statusCode' => 500,
                    'url' => ''
                ]);
            }
            $userId = $mobile->userId;
            if (Auth::loginUsingId($userId, $request->input('remember'))) {

            }
        } else {
            if (Auth::attempt([$input, $password])) {
                return response()->json([
                    'statusCode' => 200,
                    'url' => '/'
                ]);
            }
        }
        $field = 'username';


//
//        if (Auth::attempt([$field, $password])) {
//            return redirect('/');
//        }
        
        return redirect('/login')->withErrors([
            'error' => 'These credentials dont match our records',
            'login' => 'what the hell?!'
        ]);
    }
    
}
