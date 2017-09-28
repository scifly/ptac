<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureStepRequest extends FormRequest {

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
            'name'              => 'required|string|max:60|unique:procedure_steps,name,' .
                $this->input('id') . ',id,' .
                'procedure_id,' . $this->input('procedure_id') . ',' .
                'approver_user_ids,' . $this->input('approver_user_ids') . ',' .
                'related_user_ids,' . $this->input('related_user_ids'),
            'approver_user_ids' => 'required|string',
            'related_user_ids'  => 'required|string',
            'remark'            => 'required|string|max:255',
            'enabled'           => 'required|boolean',
        ];
    }

    public function wantsJson() { return true; }

    protected function prepareForValidation() {

        $input = $this->all();
        if (isset($input['approver_user_ids'])) {
            $input['approver_user_ids'] = implode(',', $input['approver_user_ids']);
        }
        if (isset($input['related_user_ids'])) {
            $input['related_user_ids'] = implode(',', $input['related_user_ids']);
        }
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        $this->replace($input);
    }
}
