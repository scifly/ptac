<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
            'name' => 'required|string|max:20|min:2',
           'max_score' => 'required|integer|min:3',
            'pass_score' => 'required|integer|min:1',
            'enabled' => 'required|boolean'

        ];
    }

    public function messages() {
        return [
            'name.required' => '科目名称不能为空',
            'name.min' => '科目名称不能少于两个字符',
            'max_score.required' => '最高分不能为空!',
//            'max_score.integer' => '分数只能为数字!',
            'pass_score.required' => '及格分不能为空!',

        ];
    }


}
