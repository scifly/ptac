<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\Overlaid;
use App\Rules\StartEnd;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EducatorAttendanceSettingRequest
 * @package App\Http\Requests
 */
class EducatorAttendanceSettingRequest extends FormRequest {
    
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
            'name'      => 'required|string|between:2,60|unique:educator_attendance_settings,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'start'     => 'required',
            'end'       => 'required',
            'school_id' => 'required|integer',
            'startend'  => ['required', new StartEnd(), new Overlaid()],
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['startend'] = [$input['start'], $input['end'], 'educator', $input['id'] ?? null];
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
