<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ParticipantRequest
 * @package App\Http\Requests
 */
class ParticipantRequest extends FormRequest {
    
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
            'educator_id'   => 'required|integer',
            'signed_up'     => 'required|date_format:"Y-m-d H:i:s"',
            'conference_id' => 'required|integer',
            'status'        => 'required|boolean',
        ];
        
    }
    
}
