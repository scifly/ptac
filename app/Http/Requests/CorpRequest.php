<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorpRequest extends FormRequest {

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
            'name'          => 'required|string|between:3,120|unique:corps,name,'
                . $this->input('id') . ',id,'
                . 'company_id,' . $this->input('company_id'),
            'department_id' => 'required|integer',
            'menu_id'       => 'required|integer',
            'corpid'        => 'required|string|alpha_num|max:18',
        ];

    }

    protected function prepareForValidation() {

        $input = $this->all();
        if (!isset($input['department_id'])) {
            $input['department_id'] = 0;
        }
        if (!isset($input['menu_id'])) {
            $input['menu_id'] = 0;
        }
        $this->replace($input);

    }

}
