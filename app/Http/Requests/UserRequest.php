<?php
namespace App\Http\Requests;

use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        
        return [
            'username'   => 'required|string|unique:users,username,' . Auth::id() . ',id',
            'realname'   => 'required|string|between:2,10',
            'english_name' => 'nullable|string|between:2, 10',
            'mobile'     => ['required', new Mobile],
            'email'      => ['nullable', 'email', new Email],
            'telephone'  => 'nullable|string|regex:/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/',
            'gender'     => 'required|boolean',
        ];
        
    }
    
}
