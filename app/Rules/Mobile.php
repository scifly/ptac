<?php
namespace App\Rules;

use App\Models\Corp;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

/**
 * Class Mobile
 * @package App\Rules
 */
class Mobile implements Rule {
    
    private $mobile;
    
    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool|false|int
     */
    public function passes($attribute, $value) {

        $this->mobile = $value;
        # 当前企业id
        $corpId = (new Corp)->corpId();
        # mobile所属用户id
        $userId = Request::input('user_id') ?? Request::input('id');
        $user = User::whereMobile($value)->first();
        $existed = !$user ? false : in_array($corpId, $user->corpIds($user->id));
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
