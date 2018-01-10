<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'name'         => 'required|string|max:255|unique:exams,name,' .
                $this->input('id') . ',id',
            'remark'       => 'required|string|max:255',
            'exam_type_id' => 'required|integer',
            'class_ids'    => 'required|string',
            'subject_ids'  => 'required|string',
            'max_scores'   => 'required|string|max:20',
            'pass_scores'  => 'required|string|max:20',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['class_ids'])) {
            $input['class_ids'] = implode(',', $input['class_ids']);
        }
        if (isset($input['subject_ids'])) {
            $input['subject_ids'] = implode(',', $input['subject_ids']);
        }
        
        $this->replace($input);
        
    }
    
}
