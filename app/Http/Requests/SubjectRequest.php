<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest {
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
        return [
            'name' => 'required|string|max:20|min:2',
            'max_score' => 'required|integer|min:3',
            'pass_score' => 'required|integer|min:1',
            'grade_ids' => 'required',
            'enabled' => 'required|boolean'
        ];
    }
    
    public function messages() {
        return [
            'name.required' => '科目名称不能为空',
            'name.min' => '科目名称不能少于两个字符',
            'max_score.required' => '最高分不能为空!',
            'pass_score.required' => '及格分不能为空!',
            'grade_ids.required' => '年级名称不能为空!'
        
        ];
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();

        if(isset($input['grade_ids']))
        {
            $input['grade_ids'] = implode(',', $input['grade_ids']);
        }
        if (isset($input['isaux']) && $input['isaux'] === 'on') {
            $input['isaux'] = 1;
        }
        if (!isset($input['isaux'])) {
            $input['isaux'] = 0;
        }
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        if (isset($input['grade_ids'])) {
            $input['grade_ids'] = implode(',', $input['grade_ids']);
        }
        $this->replace($input);
        
    }
    
    
}
