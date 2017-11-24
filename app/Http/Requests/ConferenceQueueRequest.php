<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'educator_id, ' . $this->input('educator_id'),
            'start'                 => 'required|datetime',
            'end'                   => 'required|datetime',
            'remark'                => 'required|string',
            'educator_id'           => 'required|integer',
            'educator_ids'          => 'required|string',
            'attendance_qrcode_url' => 'required|url',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!isset($input['attended_educator_ids'])) {
            $input['attended_educator_ids'] = '';
        }
        $this->replace($input);
        
    }
    
}
