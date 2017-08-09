<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'gender' => 'required|boolean',
            'realname' => 'required|string|max:60',
            'avatar_url' => 'required|string|max:255',
            'wechatid' => 'required|string|max:255'
        ];
    }
}
