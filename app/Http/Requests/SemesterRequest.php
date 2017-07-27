<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SemesterRequest extends FormRequest {
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
            'school_id' => 'required|integer',
            'name' => 'required|string|max:60',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'enabled' => 'required|boolean'
        ];
    }
}
