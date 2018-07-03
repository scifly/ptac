<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AttendanceMachineRequest
 * @package App\Http\Requests
 */
class AttendanceMachineRequest extends FormRequest {
    
    use ModelTrait;
    
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
            'name'      => 'required|string|between:2,60|unique:attendance_machines,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id') . ',' .
                'machineid,' . $this->input('machineid'),
            'location'  => 'required|string|between:2,255',
            'machineid' => 'required|string|between:2,20',
            'school_id' => 'required|integer',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
