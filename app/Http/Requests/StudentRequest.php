<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'student_number' => 'required|integer|max:32|min:2',
            'card_number' => 'required|integer|max:32|min:2',
            'remark' => 'required|string|max:32|min|2'

        ];
    }

    public function messages() {
        return [
            'student_number.required' => '学号不能为空!',
            'student_number.max' => '学号长度最大为32位!',
            'student_number.min' => '学号最小长度为2位!',
            'card_number.required' => '卡号不能为空!',
            'card_number.max' => '卡号长度最大为32位!',
            'card_number.min' => '卡号长度最小为2位!',
            'remark.required' => '备注不能为空!',
            'remark.max' => '不能超过32个字符!',
            'remark.min' => '备注不能少于2个字符!'
        ];
    }

    protected function formatErrors(Validator $validator) {
        return $validator->errors()->all();
    }
}
