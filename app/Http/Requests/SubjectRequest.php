<?php
namespace App\Http\Requests;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name'       => 'required|string|between:2,20|unique:subjects,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            // 'max_score,' . $this->input('max_score') . ',' .
            // 'pass_score,' . $this->input('pass_score') . ',' .
            // 'isaux,' . $this->input('isaux'),
            'max_score'  => 'required|numeric',
            'pass_score' => 'required|numeric',
            'school_id'  => 'required|integer',
            'grade_ids'  => 'required',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['grade_ids'])) {
            $input['grade_ids'] = implode(',', $input['grade_ids']);
        }
        $input['school_id'] = School::schoolId();
        $this->replace($input);
        
    }
    
}
