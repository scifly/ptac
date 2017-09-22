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
        if (User::whereUsername($input)->first()) {

            $field = 'username';

        } elseif (User::whereEmail($input)->first()) {
            $field = 'email';

        } else {
            $mobile = Mobile::where('mobile',$input)->where('isdefault', 1)->first();


            if (!$mobile->user_id) {
                return response()->json(['statusCode' => 500]);
            }
            $username = User::whereId($mobile->user_id)->first()->username;

//            $userId = $mobile->user_id;
            if (Auth::attempt(['username' => $username, 'password' =>$password ], $request->input('remember'))) {
                return response()->json([
                    'statusCode' => 200,
                    'url' => '../public'
                ]);
            }else{
                return response()->json(['statusCode' => 500]);
            }
        }

        if (Auth::attempt([$field => $input, 'password' =>$password])) {
            return response()->json([
                'statusCode' => 200,
                'url' => '../public'
            ]);
        }
        return response()->json(['statusCode' => 500]);


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
