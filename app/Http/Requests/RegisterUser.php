<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUser extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return false;
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'realname' => 'required|string|max:255',
            'group_id' => '',
            'gender' => 'required|boolean',
            'username' => 'required|string|between:6,30',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|size:11|regex:/^0?(13|14|15|17|18)[0-9]{9}$/',
            'password' => 'required|string|min:8|confirmed',
        ];
        
    }
    
}
