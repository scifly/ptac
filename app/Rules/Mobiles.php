<?php
namespace App\Rules;

use App\Models\Mobile;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class Mobiles implements Rule {
    
    const PHONEREG = '/^1[34578][0-9]{9}$/';
    private $value;


    public function passes($attribute, $value) {
        Log::debug($value);
        $this->value = $value;
        if (!isset($value['id'])) {
            $value['id'] = 0;
        }
        $mobile = Mobile::whereMobile($value['mobile'])
            ->where('id', '!=', $value['id'])
            ->get()->toArray();

        if (!preg_match(self::PHONEREG, $value['mobile'])) {
            return false;
        }
        if(Request::isMethod('put')) {
            if (isset($value['user_id']) && $value['user_id'] != 0) {
                $userMobile = Mobile::whereMobile($value['mobile'])
                    ->where('id', '!=', $value['id'])
                    ->where('user_id', '!=', $value['user_id'])
                    ->get()->toArray();

                if ($userMobile) {
                    return false;
                }
            }
        }else {
            if ($mobile) {
                return false;
            }
        }

        
        return true;
    }
    
    public function message() {
        $number = $this->value;
        
        return "手机号 {$number['mobile']} 已存在或者格式不正确";
    }
}
