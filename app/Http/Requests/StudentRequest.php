<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $input = $this->all();
        $rules = [
//            'name' => 'required|string|between:2,30|unique:users,name,' .
//                $this->input('user_id') . ',id,' .
//                'mobile,' . $this->input('mobile') ,
            'student.student_number' => 'required|alphanum|between:2,32',
            'student.card_number' => 'required|alphanum|between:2,32',
            'student.remark' => 'required|string|between:2,32',
            'user.realname' => 'required|string',
            'user.gender' => 'required|boolean',
            'user.email' => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
//            'mobile.mobile' => 'required|string|unique:mobiles,mobile,'
//                . $this->input('user_id') . ',user_id',
            'student.student_number' => 'required|alphanum|between:2,32|unique:students,student_number,'
                . $this->input('user_id') . ',user_id,' .
                'card_number,' . $this->input('student.card_number'),
            'student.birthday' => 'required',

        ];

        $validateRules=[];
        foreach ($input['mobile'] as $index => $mobile) {
            $rule =[
                'mobile.'.$index.'.mobile' => 'required|string|size:11|regex:/^1[34578][0-9]{9}$/|' .
                    'unique:mobiles,mobile,' . $this->input('mobile.' . $index . '.id') . ',id',
                'mobile.'.$index.'.isdefault' => 'required|boolean',
                'mobile.'.$index.'.enabled' => 'required|boolean'
            ];
            $validateRules =array_merge($rules,$rule,$validateRules);
            unset($rule);
        }
        return $validateRules;
    }
    
//    public function messages() {
//        return [
//            'user.realname.required' => '学生姓名不能为空',
//            'student.student_number.required' => '学号不能为空!',
//            'student.student_number.max' => '学号长度最大为32位!',
//            'student.student_number.min' => '学号最小长度为2位!',
//            'student.card_number.required' => '卡号不能为空!',
//            'student.card_number.max' => '卡号长度最大为32位!',
//            'student.card_number.min' => '卡号长度最小为2位!',
//            'student.remark.birthday' => '生日不能为空!',
//            'student.remark.required' => '备注不能为空!',
//            'student.remark.max' => '不能超过255个字符!',
//            'student.remark.min' => '备注不能少于2个字符!',
//        ];
//    }
    
//
//    public function wantsJson() {
//
//        return true;
//
//    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        dd($input);
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
        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            foreach ($input['mobile'] as $index => $mobile) {
                if ($index == $defaultIndex) {
                    $input['mobile'][$index]['isdefault'] = 1;
                }else{
                    $input['mobile'][$index]['isdefault'] = 0;
                }
                if (!isset($mobile['enabled'])) {
                    $input['mobile'][$index]['enabled'] = 0;
                }else{
                    $input['mobile'][$index]['enabled'] = 1;
                }
            }
        }
        $this->replace($input);
    }
    
}
