<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest {
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
            'name' => 'required|string|between:2,30|unique:users,name,' .
                $this->input('user_id') . ',id,' .
                'mobile,' . $this->input('mobile') ,
            'student_number' => 'required|alphanum|between:2,32',
            'card_number' => 'required|alphanum|between:2,32',
            'remark' => 'required|string|between:2,32',
        ];
    }
    
    public function messages() {
        return [
            'name.required' => '学生姓名不能为空',
            'student_number.required' => '学号不能为空!',
            'student_number.max' => '学号长度最大为32位!',
            'student_number.min' => '学号最小长度为2位!',
            'card_number.required' => '卡号不能为空!',
            'card_number.max' => '卡号长度最大为32位!',
            'card_number.min' => '卡号长度最小为2位!',
            'remark.required' => '备注不能为空!',
            'remark.max' => '不能超过32个字符!',
            'remark.min' => '备注不能少于2个字符!',
        ];
    }
    
    
    public function wantsJson() {
        
        return true;
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        
        if (isset($input['student']['oncampus']) && $input['student']['oncampus'] === 'on') {
            $input['student']['oncampus'] = 1;
        }
        if (!isset($input['student']['oncampus'])) {
            $input['student']['oncampus'] = 0;
        }
        if (isset($input['user']['enabled']) && $input['user']['enabled'] === 'on') {
            $input['user']['enabled'] = 1;
        }
        if (!isset($input['user']['enabled'])) {
            $input['user']['enabled'] = 0;
        }
        $this->replace($input);
    }
    
}
