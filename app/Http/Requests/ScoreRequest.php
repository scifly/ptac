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
            'class_rank' => 'required|numeric|max:1000',
            'grade_rank' => 'required|numeric|max:10000',
            'score' => 'required|numeric|max:1000'
        ];
    }

    public function messages() {
        return [
            'class_rank.required' => '班级排名不能为空',
            'class_rank.max' => '不超过5个数字',
            'class_rank.numeric' => '不超过5个数字',
            'grade_rank.required' => '年级排名不能为空',
            'grade_rank.max' => '不超过5个数字',
            'grade_rank.numeric' => '不超过5个数字',
            'score.required' => '分数不能为空',
            'score.max' => '分数不能超过3位数字',
            'score.numeric' => '分数不能超过5位数字'
        ];
    }
    protected function formatErrors(Validator $validator) {
        return $validator->errors()->all();
    }
}
