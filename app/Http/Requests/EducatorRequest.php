<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EducatorRequest extends FormRequest {
    
//    protected $strings_key = [
//        'user_id' => '教职员工',
//        'team_ids' => '所属组',
//        'school_id' => '所属学校',
//        'sms_quote' => '可用短信条数'
//    ];
//    protected $strings_val = [
//        'required' => '为必填项',
//        'string' => '必须为字符串',
//        'integer' => '必须为整数',
//        'unique' => '不唯一',
//
//    ];
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        $input = $this->all();
//        dd($input['mobile']);

        $rules =  [

            'educator.school_id' => 'required|integer',
            'classSubject' => 'required|array',
        //            'educator.subject_ids' => 'required|array',
            'user.group_id' => 'required|integer',
            'user.username' => 'required|string|unique:users,username,' .
                $this->input('user_id') . ',id',
            'user.realname' => 'required|string',
            'user.gender' => 'required|boolean',
            'user.enabled' => 'required|boolean',
            'user.email' => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
            'user.password' => 'required|string|min:3',
        //            'mobile.*.number' => 'required|string|size:11|regex:/^0?(13|14|15|17|18)[0-9]{9}$/|' .
        //                'unique:mobiles,mobile,' . $this->input('mobile.*.id') . ',id',
        //            'mobile.*.isdefault' => 'required|boolean',
        //            'mobile.*.enabled' => 'required|boolean',

        ];
        $validateRules=[];
        foreach ($input['mobile'] as $index => $mobile) {
            $rule =[
                'mobile.'.$index.'.mobile' => 'required|string|size:11|regex:/^1[34578][0-9]{9}$/|' .
                    'unique:mobiles,mobile,' . $this->input('mobile.' . $index . '.id') . ',id',
                'mobile.'.$index.'.isdefault' => 'required|boolean',
                'mobile.'.$index.'.enabled' => 'required|boolean'
            ];
            $validateRules =array_merge($rules,$rule,$validateRules);
            unset($rule);
        }
        return $validateRules;

        
    }
//
//    public function messages() {
//
//        $rules = $this->rules();
//        $k_array = $this->strings_key;
//        $v_array = $this->strings_val;
//        $array = [];
//        foreach ($rules as $key => $value) {
//            $new_arr = explode('|', $value);//分割成数组
//            foreach ($new_arr as $k => $v) {
//                $head = strstr($v, ':', true);//截取:之前的字符串
//                if ($head) {
//                    $v = $head;
//                }
//                $array[$key . '.' . $v] = $k_array[$key] . $v_array[$v];
//            }
//        }
//
//        return $array;
//
//    }
    
    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['user']['enabled']) && $input['user']['enabled'] === 'on') {
            $input['user']['enabled'] = 1;
        }
        if (!isset($input['user']['enabled'])) {
            $input['user']['enabled'] = 0;
        }
        if (isset($input['user']['gender']) && $input['user']['gender'] === 'on') {
            $input['user']['gender'] = 1;
        }
        if (!isset($input['user']['gender'])) {
            $input['user']['gender'] = 0;
        }
        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            foreach ($input['mobile'] as $index => $mobile) {
                if ($index == $defaultIndex) {
                    $input['mobile'][$index]['isdefault'] = 1;
                }else{
                    $input['mobile'][$index]['isdefault'] = 0;
                }
                if (!isset($mobile['enabled'])) {
                    $input['mobile'][$index]['enabled'] = 0;
                }else{
                    $input['mobile'][$index]['enabled'] = 1;
                }
            }
        }
//        dd($input['mobile']);

//        dd($this->input('mobile.*.mobile'));
        $this->replace($input);
        
    }
    
}
