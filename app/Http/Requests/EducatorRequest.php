<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EducatorRequest extends FormRequest
{

    protected $rules = [
        'user_id' => 'required|integer',
        'team_ids' => 'required|string',
        'school_id' => 'required|integer',
        'sms_quote' => 'required|integer'
    ];
    protected $strings_key = [
        'user_id' => '教职员工',
        'team_ids' => '所属组',
        'school_id' => '所属学校',
        'sms_quote' => '可用短信条数'
    ];
    protected $strings_val = [
        'required'=> '为必填项',
        'string'=> '必须为字符串',
        'integer'=> '必须为整数',
    ];


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return $this->rules;

    }

    public function messages(){
        
        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = [];
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
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        if (isset($input['team_ids'])) {
            $input['team_ids'] = implode(',', $input['team_ids']);
        }
        $this->replace($input);
        
    }
    
}
