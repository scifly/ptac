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
            'name'            => 'required|string|between:2,255',
            'method'          => 'required|string|between:2,255',
            'controller'      => 'required|string|between:2,255',
            'remark'          => 'nullable|string|between:2,255',
            'view'            => 'nullable|string|between:2,255',
            'route'           => 'nullable|string|between:2,255',
            'js'              => 'nullable|string|between:2,255',
            'enabled'         => 'required|boolean',
            'action_type_ids' => 'nullable|string|between:1,60',
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
        if (isset($input['action_type_ids'])) {
            $input['action_type_ids'] = implode(',', $input['action_type_ids']);
        }
        $this->replace($input);
        
    }
    
}
