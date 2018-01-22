<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
    
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
        $rule=[];
        if($this->method()==="PUT"){
            $rule=[
                'username'      => 'required|string|unique:users,username,'.
                    $this->input('id') . ',id',
                'email'         => 'nullable|email|unique:users,email,' .
                    $this->input('id') . ',id',
            ];
        }
        return $rule;

    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['action_type_ids'])) {
            $input['action_type_ids'] = implode(',', $input['action_type_ids']);
        }
        $this->replace($input);
        
    }
    
}
