<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {return true;}

    public function rules() {

        return [
            'name' => 'required|string|max:60|unique:procedures,name,' .
                $this->input('id') . ',id,' .
                'procedure_type_id,' . $this->input('procedure_type_id') . ',' .
                'school_id,' . $this->input('school_id'),
            'remark' => 'required|string|max:255',
        ];

    }
    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        $this->replace($input);
    }
}
