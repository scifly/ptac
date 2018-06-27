<?php
namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Email implements Rule {
    
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
    
        $users = User::whereEmail($value)->get();
        $u = new User;
        foreach ($users as $user) {
            if (
                $user->id != Auth::id() &&
                $user->email == $value &&
                !empty(array_intersect($u->corpIds(Auth::id()), $u->corpIds($user->id)))
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
