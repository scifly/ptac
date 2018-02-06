<?php
namespace App\Http\Requests;

use App\Models\School;
use App\Rules\Overlaid;
use App\Rules\StartEnd;
use Illuminate\Foundation\Http\FormRequest;

class SemesterRequest extends FormRequest {
    
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
            'school_id'  => 'required|integer',
            'name'       => 'required|string',
            'remark'     => 'nullable|string',
            'start_date' => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:start_date',
            'enabled'    => 'required|boolean',
            'startend' => [
                'required', new StartEnd(), new Overlaid()
            ]
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $start = substr($input['start_date'],0,10);
        $end = substr($input['end_date'],0,10);
        $input['school_id'] = School::schoolId();
        $input['startend'] = [
            $start, $end, 'semester', $input['id'] ?? null
        ];
        $this->replace($input);
        
    }
    
}
