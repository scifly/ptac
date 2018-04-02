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
        
        $rule = [];
        if ($this->method() === "PUT") {
            $rule = [
                'username'   => 'required|string|unique:users,username,' .
                    $this->input('id') . ',id',
                'email'      => 'nullable|email|unique:users,email,' .
                    $this->input('id') . ',id',
                'group_id'   => 'required|integer',
                'password'   => 'required|string|min:6|confirmed',
                'gender'     => 'required|boolean',
                'realname'   => 'required|string|between:2,10',
                'avatar_url' => 'required|url',
                'userid'     => 'required|string|unique:users,userid,' .
                    $this->input('id') . ',id',
                'enabled'    => 'required|boolean',
            ];
        }
        
        return $rule;
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['avatar_url'] = ''; # 需从企业微信后台同步
        $this->replace($input);
        
    }
    
}
