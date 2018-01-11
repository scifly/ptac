<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureTypeRequest extends FormRequest {
    
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
            'name'    => 'required|string|max:60|unique:procedure_types,name,' .
                $this->input('id') . ',id',
            'remark'  => 'required|string|max:255',
            'enabled' => 'required|boolean',
        ];
    }
    
}
