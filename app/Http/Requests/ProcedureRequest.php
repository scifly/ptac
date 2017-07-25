<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureRequest extends FormRequest {
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
            'procedure_type_id' => 'required|integer',
            'school_id' => 'required|integer',
            'name' => 'required|string|max:60',
            'remark' => 'required|string|max:255',
            'enabled' => 'required|boolean'
        ];
    }
}
