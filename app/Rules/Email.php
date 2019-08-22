<?php
namespace App\Rules;

use App\Models\Corp;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

/**
 * Class Email
 * @package App\Rules
 */
class Email implements Rule {
    
    private $email;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
    
        $this->email = $value;
        # 当前企业id
        $corpId = (new Corp)->corpId();
        # email所属用户id
        $userId = Request::input('user_id') ?? Request::input('id');
        # email所属用户
        $user = User::whereEmail($value)->first();
        $existed = !$user ? false : in_array($corpId, $user->corpIds($user->id));
        Request::method() == 'POST' ?: $existed &= $user->id != $userId;

        return !$existed;
    
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        
        return "电子邮件: {$this->email} 已存在";
        
    }
    
}
