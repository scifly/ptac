<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return false;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'corp_id' => 'required|integer',
            'school_type_id' => 'required|integer',
            'enabled' => 'required|boolean'
        ];
    }
}
