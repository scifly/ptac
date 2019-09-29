<?php
namespace App\Rules;

use App\Helpers\ModelTrait;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

/**
 * Class Mobile
 * @package App\Rules
 */
class Mobile implements Rule {
    
    use ModelTrait;
    
    private $mobile;
    
    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool|false|int
     */
    public function passes($attribute, $value) {

        $this->mobile = $value;
        # mobile所属用户id
        $userId = Request::input('user_id') ?? Request::input('id');
        $user = User::whereMobile($value)->first();
        $existed = !$user ? false : $user->corpIds($user->id)->flip()->has($this->corpId());
        Request::method() == 'POST' ?: $existed &= $user->id != $userId;
        
        return $existed && preg_match('/^1[3456789][0-9]{9}$/', $value);
        
    }
    
    /**
     * @return string
     */
    public function message() {
        
        return "手机号 {$this->mobile} 已存在或者格式不正确";
        
    }
    
}
