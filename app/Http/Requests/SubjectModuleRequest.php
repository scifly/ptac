<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectModuleRequest extends FormRequest
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
            'weight' => 'required|integer',

        ];
    }

    public function messages() {
        return [
            'name.required' => '名称不能为空!',
            'name.max' => '名称长度最大为32位!',
            'name.min' => '名称不能少于2个字符!',
            'weight.required' => '权重不能为空!',
            'weight.integer' => '权重只能为数字!',
            ''
        ];
    }
}
