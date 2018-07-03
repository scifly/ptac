<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ConferenceQueueRequest
 * @package App\Http\Requests
 */
class ConferenceQueueRequest extends FormRequest {
    
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
            'name'                  => 'required|string|between:4,120|unique:conference_queues,name,' .
                $this->input('id') . ',id,' .
                'conference_room_id,' . $this->input('conference_room_id') . ',' .
                'user_id, ' . $this->input('user_id'),
            'start'                 => 'required|datetime',
            'end'                   => 'required|datetime',
            'remark'                => 'required|string',
            'user_id'               => 'required|integer',
            'educator_ids'          => 'required|string',
            'conference_room_id'    => 'required|integer',
            'attendance_qrcode_url' => 'required|url',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!isset($input['attended_educator_ids'])) {
            $input['attended_educator_ids'] = '';
        }
        if (!isset($input['user_id'])) {
            $input['user_id'] = Auth::id();
        }
        $this->replace($input);
        
    }
    
}
