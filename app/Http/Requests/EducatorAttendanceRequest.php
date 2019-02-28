<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EducatorAttendanceRequest
 * @package App\Http\Requests
 */
class EducatorAttendanceRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return true;
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'user_id'    => 'required|integer',
            'clocked_at' => 'required|date',
            'longitude'  => 'required|numeric',
            'latitude'   => 'required|numeric',
            'direction'  => 'required|integer',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['longitude'] = $input['longitude'] ?? 0;
        $input['latitude'] = $input['latitude'] ?? 0;
        $this->replace($input);
        
    }
    
}
