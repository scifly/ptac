<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\Overlaid;
use App\Rules\StartEnd;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SemesterRequest
 * @package App\Http\Requests
 */
class SemesterRequest extends FormRequest {
    
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
            'school_id'  => 'required|integer',
            'name'       => 'required|string',
            'remark'     => 'nullable|string',
            'start_date' => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:start_date',
            'enabled'    => 'required|boolean',
            'startend'   => [
                'required', new StartEnd(), new Overlaid(),
            ],
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $input['startend'] = [
            $input['start_date'],
            $input['end_date'],
            'semester',
            $input['id'] ?? null,
        ];
        
        $this->replace($input);
        
    }
    
}
