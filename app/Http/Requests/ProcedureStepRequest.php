<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureStepRequest extends FormRequest {
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
            'name' => 'required|string|max:60',
            'approver_user_ids' => 'required|string|max:255',
            'related_user_ids' => 'required|string|max:255',
            'remark' => 'required|string|max:255',
            'enabled' => 'required|boolean'
        ];
    }
}
