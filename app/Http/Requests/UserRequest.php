<?php
namespace App\Http\Requests;

use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class UserRequest
 * @package App\Http\Requests
 */
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
        
        $paths = explode('/', Request::path());
        if ($paths[1] == 'reset') {
            return [
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8'
            ];
        }
        
        return [
            'username'     => 'required|string|unique:users,username,' . Auth::id() . ',id',
            'realname'     => 'required|string|between:2,10',
            'english_name' => 'nullable|string|between:2,20',
            'mobile'       => ['required', new Mobile],
            'email'        => ['nullable', 'email', new Email],
            'gender'       => 'required|boolean',
        ];
        
    }
    
}
