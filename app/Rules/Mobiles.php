<?php
namespace App\Rules;

use App\Models\Mobile;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Mobiles implements Rule {
    
    const PHONEREG = '/^1[34578][0-9]{9}$/';
    private $value;


    public function passes($attribute, $value) {
        $this->value = $value;
        if (!isset($value['id'])) {
            $value['id'] = 0;
        }
        $mobile = Mobile::whereMobile($value['mobile'])
            ->where('id', '!=', $value['id'])
            ->get()->toArray();
        if ($mobile || !preg_match(self::PHONEREG, $value['mobile'])) {
            return false;
        }
        
        return true;
    }
    
    public function message() {
        $number = $this->value;
        
        return "手机号 {$number['mobile']} 已存在或者格式不正确";
    }
}
