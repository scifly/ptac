<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureTypeRequest extends FormRequest {

    protected $rules = [
        'name' => 'required|string|max:60',
        'remark' => 'required|string|max:255',
        'enabled' => 'required|boolean'
    ];
    protected $strings_key = [
        'name' => '类型名称',
        'remark' => '备注',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required'=> '为必填项',
        'string'=> '为字符串',
        'max'=> '最大为:max',
        'boolean'=> '为0或1',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return $this->rules;
    }

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
}
