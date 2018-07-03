<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConferenceParticipantRequest
 * @package App\Http\Requests
 */
class ConferenceParticipantRequest extends FormRequest {
    
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
            'educator_id'         => 'required|integer',
            'attendance_time'     => 'required|date_format:"Y-m-d H:i:s"',
            'conference_queue_id' => 'required|integer',
            'status'              => 'required|boolean',
        ];
        
    }
    
}
