<?php
namespace App\Rules;

use App\Models\Corp;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Mobile implements Rule {
    
    private $mobile;
    
    public function passes($attribute, $value) {

        $this->mobile = is_array($value) ? $value['mobile'] : $value;
        $action = Request::method();
        $user = new User;
        $corp = new Corp;
        if (is_array($value)) {
            $userId = $action == 'POST' ? Auth::id() : Request::input('user_id');
        } else {
            $userId = Auth::id();
        }
        # 即将被添加的手机号码所属企业的corp_id
        $_corpIds = $action == 'POST' ? [$corp->corpId()] : $user->corpIds($userId);
        # 已有的相同手机号码
        $mobiles = \App\Models\Mobile::whereMobile($this->mobile)->get();
        foreach ($mobiles as $mobile) {
            $corpIds = $user->corpIds($mobile->user_id);
            if (
                $this->mobile == $mobile->mobile &&
                !empty(array_intersect($_corpIds, $corpIds)) &&
                ($action == 'PUT' ? $mobile->user_id != $userId : true)
            ) {
                return false;
            }
        }
        
        return preg_match('/^1[34578][0-9]{9}$/', $this->mobile);
        
    }
    
    public function message() {
        
        return "手机号 {$this->mobile} 已存在或者格式不正确";
        
    }
    
}
