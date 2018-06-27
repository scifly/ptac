<?php
namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Mobile implements Rule {
    
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
    
        $mobiles = \App\Models\Mobile::whereMobile($value)->get();
        $user = new User;
        foreach ($mobiles as $mobile) {
            if (
                $mobile->user_id != Auth::id() &&
                $mobile->mobile == $value &&
                !empty(array_intersect($user->corpIds(Auth::id()), $user->corpIds($mobile->user_id)))
            ) {
                return false;
            }
        }
        
        return preg_match('/^1[34578][0-9]{9}$/', $value);
    
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        
        return ':attribute号码格式不正确或已存在';
        
    }
    
}
