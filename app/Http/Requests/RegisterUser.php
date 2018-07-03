<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RegisterUser
 * @package App\Http\Requests
 */
class RegisterUser extends FormRequest {
    
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
            'realname' => 'required|string|max:255',
            'group_id' => 'required|integer',
            'gender'   => 'required|boolean',
            'username' => 'required|string',
            'email'    => 'required|string|email|max:255|unique:users',
            'mobile'   => 'required|string|size:11|regex:/^0?(13|14|15|17|18)[0-9]{9}$/',
            'password' => 'required|string|min:6|confirmed',
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
        $input['password'] = bcrypt($input['password']);
        $this->replace($input);
        
    }
    
}
