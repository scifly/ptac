<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreRequest extends FormRequest {
    
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
            'student_id' => 'required|integer|unique:scores,student_id,' .
                $this->input('id') . ',id,' .
                'subject_id,' . $this->input('subject_id') . ',' .
                'exam_id,' . $this->input('exam_id'),
            'score'      => 'required|numeric',
        ];
    }
    
    public function messages() {
        return [
            'score.required' => '分数不能为空',
            'score.unique'   => '已有该条记录',
            'score.max'      => '分数不能超过3位数字',
            'score.numeric'  => '分数不能超过5位数字',
        ];
    }
    
    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        $this->replace($input);
    }
    
}
