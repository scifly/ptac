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

    protected function prepareForValidation() {

        $input = $this->all();
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
