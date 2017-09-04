<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureRequest extends FormRequest {
    
    protected $rules = [
        'procedure_type_id' => 'required|integer',
        'school_id' => 'required|integer',
        'name' => 'required|string|max:60',
        'remark' => 'required|string|max:255',
        'enabled' => 'required|boolean'
    ];
    protected $strings_key = [
        'procedure_type_id' => '流程类型',
        'school_id' => '学校',
        'name' => '流程名称',
        'remark' => '备注',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'integer' => '为整数',
        'string' => '为字符串',
        'max' => '最大为:max',
        'array' => '必须为数组',
        'boolean' => '为0或1',
    ];
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    public function messages() {
        
        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = array();
        
        foreach ($rules as $key => $value) {
            $new_arr = explode('|', $value);//分割成数组
            foreach ($new_arr as $k => $v) {
                $head = strstr($v, ':', true);//截取:之前的字符串
                if ($head) {
                    $v = $head;
                }
                $array[$key . '.' . $v] = $k_array[$key] . $v_array[$v];
            }
        }
        
        return $array;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return $this->rules;
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
        $this->replace($input);
    }
}
