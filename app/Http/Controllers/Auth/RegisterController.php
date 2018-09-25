<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
 * 注册
 *
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller {
    
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;
    
    static $category = 2; # 其他类型控制器
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    protected $user;
    
    /**
     * Create a new controller instance.
     * @param User $user
     */
    public function __construct(User $user) {
        
        $this->middleware('guest');
        $this->user = $user;
        
    }
    
    /**
     * @param RegisterUser $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(RegisterUser $request) {
        event(new Registered($user = $this->create($request->all())));
        $this->guard()->login($user);
        
        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
        
    }
    
    /**
     * 创建用户
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data) {
        return $this->user->create([
            'realname' => $data['realname'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'mobile'   => $data['mobile'],
            'password' => bcrypt($data['password']),
        ]);
        
    }
}
