<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StudentAttendanceRequest
 * @package App\Http\Requests
 */
class StudentAttendanceRequest extends FormRequest {
    
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
            'sn'         => 'required|string|between:5,32',
            'punch_time' => 'required|date',
            'inorout'    => 'required|integer',
            'media_id'   => 'required|integer',
            'longitude'  => 'required|numeric',
            'latitude'   => 'required|numeric',
            'machineid'  => 'required|string|between:1,20',
        ];
        
    }
    
    /**
     * @return bool
     */
    public function wantsJson() {
        return true;
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['inorout'] = $input['inorout'] ?? 2;
        $input['longitude'] = $input['longitude'] ?? 0;
        $input['latitude'] = $input['latitude'] ?? 0;
        $input['machineid'] = $input['attendid'];
        $input['media_id'] = 0;
        $this->replace($input);
        
    }
    
}
