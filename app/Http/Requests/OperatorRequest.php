<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OperatorRequest extends FormRequest {
    
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
            'Operator.company_id' => 'required|integer',
            'Operator.user_id' => 'required|integer|unique:operators,user_id,' .
                $this->input('Operator.id') . ',id',
            'Operator.school_ids' => 'required|string',
            'Operator.group_id' => 'required|integer',
            'User.username' => 'required|string|unique:users,username,' .
                $this->input('User.id') . ',id',
            'User.realname' => 'required|string',
            'User.gender' => 'required|boolean',
            'User.enabled' => 'required|boolean',
            'User.email' => 'nullable|email|unique:users,email,' .
                $this->input('User.id') . ',id',
            'User.password' => 'required|string|min:60',
            'Mobile.mobile' => 'required|string|size:11|regex:/^0?(13|14|15|17|18)[0-9]{9}$/|' .
                'unique:mobiles,mobile,' . $this->input('Mobile.id') . ',id',
            'Mobile.isdefault' => 'required|boolean',
            'Mobile.user_id' => 'required|integer',
            'Mobile.enabled' => 'required|boolean'
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['User.enabled']) && $input['User.enabled'] === 'on') {
            $input['User.enabled'] = 1;
        }
        if (!isset($input['User.enabled'])) {
            $input['User.enabled'] = 0;
        }
        if (isset($input['Operator.school_ids'])) {
            $input['Operator.school_ids'] = implode(',', $input['Operator.school_ids']);
        }
        
    }
    
}
