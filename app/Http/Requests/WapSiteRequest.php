<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WapSiteRequest extends FormRequest {
    
    
    protected $strings_key = [
        'school_id' => '所属学校',
        'site_title' => '首页抬头',
        'media_ids' => '轮播图',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'string' => '为字符串',
        'max' => '最大为:max',
        'integer' => '必须为整数',
        'boolean' => '为0或1',
        'unique' => '不唯一',
    ];
    
    
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
    
    public function rules() {
        return [
            'school_id' => 'required|integer|unique:wap_sites,school_id,' .
                $this->input('id') . ',id',
            'site_title' => 'required|string|max:255',
            'media_ids' => 'required|string',
            'enabled' => 'required|boolean'
        ];
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
        if (isset($input['media_ids'])) {
            $input['media_ids'] = implode(',', $input['media_ids']);
        }
        $this->replace($input);
        
    }
}
