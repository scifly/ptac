<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustodianRequest extends FormRequest
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
            //
        ];
    }

    protected function prepareForValidation() {

        $input = $this->all();

        $this->replace($input);
    }
}
