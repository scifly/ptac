<?php
namespace App\Http\Requests;

use App\Rules\Mobiles;
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
        $rules = [
            'card_number'    => 'required|alphanum|between:2,32|unique:students,card_number,'
            . $this->input('user_id') . ',user_id',
            'user.realname'          => 'required|string',
            'user.gender'            => 'required|boolean',
            'user.email'             => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
            'mobile.*'               => [
                'required', new Mobiles(),
            ],
            'student_number' => 'required|alphanum|between:2,32|unique:students,student_number,'
                . $this->input('user_id') . ',user_id',
            'birthday'       => 'required',
        ];
        return $rules;
    }

    public function messages() {
        return [
            'user.realname.required' => '学生姓名不能为空',
            'student_number.required' => '学号不能为空!',
            'student_number.max' => '学号长度最大为32位!',
            'student_number.min' => '学号最小长度为2位!',
            'student_number.unique' => '已有该学号！',
            'card_number.required' => '卡号不能为空!',
            'card_number.unique' => '已有该卡号!',
            'birthday' => '生日不能为空!',
            'user.email.email' => '邮箱格式不正确!',
        ];
    }
//
//    public function wantsJson() {
//
//        return true;
//
//    }
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['oncampus']) && $input['oncampus'] === 'on') {
            $input['oncampus'] = 1;
        }
        if (!isset($input['oncampus'])) {
            $input['oncampus'] = 0;
        }

        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            foreach ($input['mobile'] as $index => $mobile) {
                if ($index == $defaultIndex) {
                    $input['mobile'][$index]['isdefault'] = 1;
                } else {
                    $input['mobile'][$index]['isdefault'] = 0;
                }
                if (!isset($mobile['enabled'])) {
                    $input['mobile'][$index]['enabled'] = 0;
                } else {
                    $input['mobile'][$index]['enabled'] = 1;
                }
            }
        }
        $this->replace($input);
    }
    
}
