<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'weight' => 'required|numeric',
        ];

    }
    
}
