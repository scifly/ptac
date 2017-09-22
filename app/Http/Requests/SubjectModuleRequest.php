<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectModuleRequest extends FormRequest {
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
            'name' => 'required|string|between:2,20|unique:subject_modules,name,' .
                $this->input('id') . ',id,' .
                'subject_id,' . $this->input('subject_id') . ',' .
                'weight,' . $this->input('weight'),
            'weight' => 'required|numeric',
        
        ];
    }
    
    public function messages() {
        
        return [
            'name.required' => '科目名称不能为空',
            'name.between' => '科目名称应该在2~20个字符之间',
            'name.unique' => '已有该记录',
            'weight.required' => '次分类权重不能为空',
            'weight.numeric' => '次分类权重必须为数字'
        
        ];
        
    }
    
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
