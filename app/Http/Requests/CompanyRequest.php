<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest {

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
            'name'          => 'required|string|between:4,40|unique:companies,name,' .
                $this->input('id') . ',id',
            'department_id' => 'required|integer',
            'menu_id'       => 'required|integer',
            'remark'        => 'required',
        ];

    }

    public function messages() {

        return [
            'name.required'   => '公司名称不能为空',
            'name.between'    => '公司名称应该在4~40个字符之间',
            'name.unique'     => '已有该记录',
            'remark.required' => '备注不能为空',
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
        if (!isset($input['department_id'])) {
            $input['department_id'] = 0;
        }
        if (!isset($input['menu_id'])) {
            $input['menu_id'] = 0;
        }
        $this->replace($input);

    }

}
