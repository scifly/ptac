<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PqRequest extends FormRequest {

    protected $strings_key = [
        'name' => '问卷名称',
        'school_id' => '所属学校',
        'start' => '开始时间',
        'end' => '结束时间',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'string' => '为字符串',
        'max' => '最大为:max',
        'integer' => '必须为整数',
        'boolean' => '为0或1',
        'unique' => '不唯一',
        'date_format' => '格式不正确',

    ];
    public function rules() {
        return [
            'name' => 'required|string|max:255|unique:poll_questionnaires,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'school_id' => 'required|integer',
            'start' => 'required|date_format:Y-m-d H:i:s' ,
            'end' => 'required|date_format:Y-m-d H:i:s',
            'enabled' => 'required|boolean'
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }

    public function messages() {

        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = [];
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
