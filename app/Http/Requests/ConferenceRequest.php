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
                'educator_id,' . $this->input('educator_id'),
            'start'        => 'required|datetime',
            'end'          => 'required|datetime',
            'remark'       => 'required|string',
            'educator_id'      => 'required|integer',
            'educator_ids' => 'required|string',
            'room_id'      => 'required|integer',
            'url'          => 'required|url',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!isset($input['educator_id'])) {
            $input['educator_id'] = $this->user()->educator->id;
        }
        $this->replace($input);
        
    }
    
}
