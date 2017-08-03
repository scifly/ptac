<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActionRequest extends FormRequest {
    
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
            'name' => 'required|string|max:255',
            'method' => 'required|string|max:255',
            'remark' => 'required|string|max:255',
            'controller' => 'required|string|max:255',
            'view' => 'string|max:255',
            'route' => 'string|max:255',
            'js' => 'string|max:255',
            'datatable' => 'boolean',
            'parsley' => 'boolean',
            'select2' => 'boolean',
            'chart' => 'boolean',
            'map' => 'boolean',
            'enabled' => 'required|boolean',
            'action_type_ids' => 'string|max:60'
        ];
    }
    
    protected function prepareForValidation() {
    
        $input = $this->all();
        if (isset($input['datatable']) && $input['datatable'] === 'on') {
            $input['datatable'] = 1;
        }
        if (isset($input['parsley']) && $input['parsley'] === 'on') {
            $input['parsley'] = 1;
        }
        if (isset($input['select2']) && $input['select2'] === 'on') {
            $input['select2'] = 1;
        }
        if (isset($input['chart']) && $input['chart'] === 'on') {
            $input['chart'] = 1;
        }
        if (isset($input['map']) && $input['map'] === 'on') {
            $input['map'] = 1;
        }
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        $input['action_type_ids'] = implode(',', $input['action_type_ids']);
        $this->replace($input);
    }
    
    
}
