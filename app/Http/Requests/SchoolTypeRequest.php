<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolTypeRequest extends FormRequest {

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
            'name'    => 'required|string|between:2,60|unique:school_types,name,' .
                $this->input('id') . ',id',
            'remark'  => 'string|between:2,255',
            'enabled' => 'required|boolean',
        ];

    }

}
