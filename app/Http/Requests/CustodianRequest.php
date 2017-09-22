<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustodianRequest extends FormRequest {
    
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
//            'user.realname' => 'required|string|between:2,255|unique:users,realname,' .
//                $this->input('user_id') . ',id,'.
//                'gender,' . $this->input('user.gender') ,
            'user.realname' => 'required|string',
            'user.gender' => 'required|boolean',
            'user.email' => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
//            'mobile.mobile' => 'required|string|unique:mobiles,mobile,'
//                . $this->input('user_id') . ',user_id',
        ];
        
    }
    
    
    public function messages() {
        
        return [
            'user.realname.required' => '监护人姓名不能为空',
        
        
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        dd($input);
        if (isset($input['user']['enabled']) && $input['user']['enabled'] === 'on') {
            $input['user']['enabled'] = 1;
        }
        if (!isset($input['user']['enabled'])) {
            $input['user']['enabled'] = 0;
        }
        $this->replace($input);
    }
    
}
