<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {

    protected $rules = [
        'group_id' => 'required|integer',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|email|max:255|unique:users',
        'gender' => 'required|boolean',
        'realname' => 'required|string|max:60',
        'avatar_url' => 'required|string|max:255',
        'wechatid' => 'required|string|max:255',
        'enabled' => 'required|boolean'
    ];

    protected $strings_key = [
        'group_id' => '分组类型',
        'username' => '用户名',
        '_token' => '记住我令牌',
        'email' => '电子邮箱',
        'gender' => '性别',
        'realname' => '姓名',
        'avatar_url' => '头像',
        'wechatid' => '微信号',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required'=> '为必填项',
        'integer' => '为整数',
        'string'=> '为字符串',
        'max'=> '最大为:max',
        'boolean'=> '为0或1',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() { return $this->rules; }

    public function messages(){

        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = array();

        foreach ($rules as $key => $value) {
            $new_arr = explode('|', $value);//分割成数组
            foreach ($new_arr as $k => $v) {
                $head = strstr($v,':',true);//截取:之前的字符串
                if ($head) {$v = $head;}
                $array[$key.'.'.$v] = $k_array[$key].$v_array[$v];
            }
        }

        return $array;
    }

    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
    
        $input = $this->all();
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) { $input['enabled'] = 0; }
        $input['password'] = '12345678';
    
        $this->replace($input);
        
    }
    
}
