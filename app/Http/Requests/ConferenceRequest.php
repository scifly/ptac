<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConferenceRequest
 * @package App\Http\Requests
 */
class ConferenceRequest extends FormRequest {
    
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
            'name'         => 'required|string|between:4,120|unique:conferences,name,' .
                $this->input('id') . ',id,' .
                'room_id,' . $this->input('room_id') . ',' .
                'user_id,' . $this->input('user_id') . ',' .
                'message_id' . $this->input('message_id'),
            'user_id'      => 'required|integer',
            'room_id'      => 'required|integer',
            'message_id'   => 'required|integer',
            'url'          => 'required|url',
            'start'        => 'required|datetime',
            'end'          => 'required|datetime',
            'remark'       => 'required|string',
            'status'       => 'required|integer'
        ];
        
    }
    
    protected function prepareForValidation() {
    
    
    
    }
    
}
