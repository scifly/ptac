<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
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
    
        $field = 'username';
        
        if (is_numeric($request->input('login'))) {
            $field = 'mobile';
        } elseif (filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        }
    
        $request->merge([$field => $request->input('login')]);
        
        if (Auth::attempt($request->only([$field, 'password']))) {
            return redirect('/');
        }

        return redirect('/login')->withErrors([
            'error' => 'These credentials dont match our records',
        ]);
    }
    
}
