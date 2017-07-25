<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest {
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
            'name' => 'required|string|max:255|min:4',
            'remark' => 'required|string|max:255',
            'corpid' => 'required|string|max:255',
            'enabled' => 'required|boolean'
        ];
    }

    public function messages() {
        return [
            'name.required' => '公司名称不能为空',
            'name.min' => '公司名称不能少于四个字符',
            'remark.required' => '备注不能为空',
            'corpid.required' => '企业号ID不能为空'
        ];
    }

    protected function formatErrors(Validator $validator) {
        return $validator->errors()->all();
    }

    public function ValidateForm() {

    }
}
