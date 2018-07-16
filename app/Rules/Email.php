<?php
namespace App\Rules;

use App\Models\Corp;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Class Email
 * @package App\Rules
 */
class Email implements Rule {
    
    private $email;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
    
        $action = Request::method();
        # 即将被添加的email对应的userId
        $userId = (is_array(Request::input('mobile')) && $action == 'PUT')
            ? Request::input('user_id') ?? Request::input('id')
            : Auth::id();
        # 即将被添加的email所属企业的corp_id
        $_corpIds = $action == 'POST'
            ? [Request::input('corp_id', (new Corp)->corpId())]
            : (new User)->corpIds($userId);
        $users = User::whereEmail($value)->get();
        Log::debug(json_encode($users->toArray()));
        foreach ($users as $user) {
            $corpIds = $user->corpIds($user->id);
            if (
                $user->email == $value &&
                !empty(array_intersect($_corpIds, $corpIds)) &&
                ($action == 'PUT' ? $user->id != $userId : true)
            ) {
                return false;
            }
        }
        
        return true;
    
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        
        return ':attribute已存在';
        
    }
    
}
