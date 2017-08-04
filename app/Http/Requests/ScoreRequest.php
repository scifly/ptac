<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ScoreRequest extends FormRequest
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
            'score' => 'required|numeric'
        ];
    }

    public function messages() {
        return [
            'score.required' => '分数不能为空',
            'score.max' => '分数不能超过3位数字',
            'score.numeric' => '分数不能超过5位数字'
        ];
    }
    public function wantsJson() { return true; }
}
