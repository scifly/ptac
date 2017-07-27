<?php

namespace App\Http\Requests;

<<<<<<< HEAD
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
=======
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest {
    
>>>>>>> refs/remotes/origin/master
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
<<<<<<< HEAD
    public function authorize()
    {
        return true;
    }

=======
    public function authorize() {
        return false;
    }
    
>>>>>>> refs/remotes/origin/master
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
<<<<<<< HEAD
    public function rules()
    {
        return [
            'name' => 'required|string|max:20|min:2',
            'remark' => 'required|string|max:20|min:2',

        ];
    }

    public function messages() {
        return [
            'name.required' => '角色名称不能为空',
            'name.min' => '角色名称不能少于2个字符',
            'name.max' => '角色名称不能大于20个字符',
            'remark.required' => '备注不能为空!',
            'remark.min' => '备注不能少于2个字符',
            'remark.max' => '备注不能大于20个字符',
        ];
    }
    protected function formatErrors(Validator $validator) {
        return $validator->errors()->all();
    }
=======
    public function rules() {
        return [
            'name' => 'required|string|max:100',
            'remark' => 'string|max:255'
        ];
    }
    
    public function wantsJson() {
        
        return true;
        
    }
    
>>>>>>> refs/remotes/origin/master
}
