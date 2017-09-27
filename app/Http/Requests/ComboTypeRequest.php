<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComboTypeRequest extends FormRequest {
    
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'name'      => 'required|string|between:2,60|unique:combo_types,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('id'),
            'amount'    => 'required|integer',
            'discount'  => 'required|integer',
            'school_id' => 'required|integer',
            'months'    => 'required|integer',
            'enabled'   => 'required|boolean',
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
