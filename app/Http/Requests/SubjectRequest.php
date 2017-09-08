<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest {
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
//            'name' => 'required|string|between:2,20|unique:subject,name' .
//                $this->input('id') . ',id,' .
//                'school_id,' . $this->input('school_id') .
//                'max_score,' . $this->input('max_score') .
//                'pass_score,' . $this->input('pass_score') .
//                'isaux,' . $this->input('isaux'),
//            'max_score' => 'required|numeric|between:3,3',
//            'pass_score' => 'required|numeric|between:2,2',
//            'grade_ids' => 'required'
        ];
    }

    public function messages() {

        return [
//            'name.required' => '科目名称不能为空',
//            'name.between' => '科目名称应该在2~20个字符之间',
//            'name.unique' => '已有该记录',
//            'max_score.required' => '最大分数不能为空',
//            'max_score.numeric' => '分数只能为数字',
//            'max_score.between' => '最大分数只能为3位数',
//            'pass_score.required' => '及格分数不能为空',
//            'pass_score.numeric' => '分数只能为数字',
//            'pass_score.between' => '及格分数只能为2位数',
//            'grade_ids.required' => '年级不能为空',
        ];

    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();

        if (isset($input['grade_ids'])) {
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

        $this->replace($input);
        
    }
    
    
}
