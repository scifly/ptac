<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest {
    
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
    public function rules() {
        
        return [
            'name' => 'required|string|between:4,40|unique:companies,name,' .
                $this->input('id') . ',id',
            'remark' => 'required',
            'corpid' => 'required|string|alpha_num|max:36'
        ];
        
    }
    
    public function messages() {
        
        return [
            'name.required' => '公司名称不能为空',
            'name.max' => '公司名称不超过40个汉字',
            'name.min' => '公司名称不能少于四个字符',
            'remark.required' => '备注不能为空',
            'corpid.required' => '企业ID不能为空',
            'corpid.max' => '36个小写字母与阿拉伯数字',
            'corpid.alpha_num' => '36个小写字母与阿拉伯数字'
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
        $this->replace($input);
        
    }
    
}
