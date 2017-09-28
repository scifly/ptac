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
            'score'       => 'required|numeric|max:1000',
            'subject_ids' => 'required|string',
        ];
    }
    
    public function messages() {
        return [
            'score.required'       => '成绩不能为空',
            'score.max'            => '成绩不能超过3位数字',
            'score.numeric'        => '成绩不能超过5位数字',
            'subject_ids.required' => '请选择计入总成绩科目',
            'subject_ids.string'   => '必须是字符串',
        ];
    }
    
    public function wantsJson() {
        return true;
    }

//    protected function prepareForValidation() {
//
//        $input = $this->all();
//        $input['subject_ids'] = implode(',', $input['subject_ids']);
//        $this->replace($input);
//        dd($input);
//    }
}
