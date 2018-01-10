<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PqChoiceRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'choice' => 'required|string|max:255|unique:poll_questionnaire_subject_choices,choice,' .
                $this->input('id') . ',id,' .
                'pqs_id,' . $this->input('pqs_id'),
            'pqs_id' => 'required|integer',
            'seq_no' => 'required|integer|max:3',
        ];
        
    }
    
}
