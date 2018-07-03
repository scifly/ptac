<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SubjectModuleRequest
 * @package App\Http\Requests
 */
class SubjectModuleRequest extends FormRequest {
    
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
            'name'   => 'required|string|between:2,20|unique:subject_modules,name,' .
                $this->input('id') . ',id,' .
                'subject_id,' . $this->input('subject_id') . ',' .
                'weight,' . $this->input('weight'),
            'subject_id' => 'required|integer',
            'weight' => 'required|numeric',
        ];
        
    }
    
}
