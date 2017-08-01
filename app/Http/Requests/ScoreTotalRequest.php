<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreTotalRequest extends FormRequest {
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
            'score' => 'required|numeric|max:1000',
            'class_rank' => 'required|numeric|max:1000',
            'grade_rank' => 'required|numeric|max:10000',
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
            'score.required' => '成绩不能为空',
            'score.max' => '成绩不能超过3位数字',
            'score.numeric' => '成绩不能超过5位数字'
        ];
    }

    public function wantsJson() {
        return true;
    }

}
