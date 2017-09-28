<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest {

    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
            'name'   => 'required|string|between:2,255|unique:groups,name,' .
                $this->input('id') . ',id',
            'remark' => 'required|string|between:2,20',
        ];

    }

    public function messages() {
        return [
            'name.required'   => '角色名称不能为空',
            'name.min'        => '角色名称不能少于2个字符',
            'name.max'        => '角色名称不能大于20个字符',
            'remark.required' => '备注不能为空!',
            'remark.min'      => '备注不能少于2个字符',
            'remark.max'      => '备注不能大于20个字符',
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
        $tabIds = [];
        if (isset($input['tabs'])) {
            foreach ($input['tabs'] as $k => $v) {
                $tabIds[] = $k;
            }
            $input['tabId'] = $tabIds;
        }
        $actionIds = [];
        if (isset($input['actions'])) {
            foreach ($input['actions'] as $k => $v) {
                $actionIds[] = $k;
            }
            $input['acitonId'] = $actionIds;
        }
        $this->replace($input);

    }

}
