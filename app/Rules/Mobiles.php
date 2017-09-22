<?php

namespace App\Rules;

use App\Models\Mobile;
use Illuminate\Contracts\Validation\Rule;

class Mobiles implements Rule {
    const PHONEREG = '/^(13[0-9]|14[579]|15[0-3,5-9]|17[0135678]|18[0-9])\\d{8}$/';
    private $value;
    public function passes($attribute, $value)
    {
        $this->value = $value;
        $mobileModel = new Mobile();
        $mobile = $mobileModel->where('mobile',$value['mobile'])
                            ->where('id','!=',$value['id'])
                            ->get()->toArray();
        if($mobile || !preg_match(self::PHONEREG, $value['mobile'])) {
            return false;
        }
        return true;
    }
    public function message()
    {
        $number = $this->value;
        return "手机号 {$number['mobile']} 已存在或者格式不正确";
    }
}
