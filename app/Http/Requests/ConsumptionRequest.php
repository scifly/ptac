<?php
namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConsumptionRequest
 * @package App\Http\Requests
 */
class ConsumptionRequest extends FormRequest {
    
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
            'student_id' => 'required|integer',
            'location'   => 'nullable|string|between:2,255',
            'machineid'  => 'nullable|string|between:2,255',
            'ctype'      => 'required|boolean',
            'amount'     => 'required|numeric',
            'ctime'      => 'required|date',
            'merchant'   => 'required|string|between:2,255',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $student = Student::whereSn($input['sn'])->first();
        $input['student_id'] = $student ? $student->id : 0;
        $this->replace($input);
        
    }
    
}
