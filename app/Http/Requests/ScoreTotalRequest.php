<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ScoreTotalRequest
 * @package App\Http\Requests
 */
class ScoreTotalRequest extends FormRequest {
    
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
            'score'          => 'required|numeric|max:1000',
            'exam_id'        => 'required|integer',
            'subject_ids'    => 'required|string',
            'na_subject_ids' => 'required|string',
        ];
        
    }
    
}

