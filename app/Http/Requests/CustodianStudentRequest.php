<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustodianStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'relationship' =>'required|string|min:2',
        ];
    }

    public function messages() {
        return [
            'relationship.required' => '关系不能为空!',
            'relationship.min' => '不能少于2个字符!',

        ];
    }

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
