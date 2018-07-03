<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProcedureRequest
 * @package App\Http\Requests
 */
class ProcedureRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'name'   => 'required|string|max:60|unique:procedures,name,' .
                $this->input('id') . ',id,' .
                'procedure_type_id,' . $this->input('procedure_type_id') . ',' .
                'school_id,' . $this->input('school_id'),
            'remark' => 'required|string|max:255',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
