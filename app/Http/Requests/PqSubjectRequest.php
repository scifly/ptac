<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PqSubjectRequest
 * @package App\Http\Requests
 */
class PqSubjectRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'subject'      => 'required|string|max:255|unique:poll_questionnaire_subjects,subject,' .
                $this->input('id') . ',id,' .
                'pq_id,' . $this->input('pq_id'),
            'pq_id'        => 'required|integer',
            'subject_type' => 'required|integer',
        ];
        
    }
    
}
