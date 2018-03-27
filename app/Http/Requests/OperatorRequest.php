<?php
namespace App\Http\Requests;

use App\Rules\Mobiles;
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
            'group_id'              => 'required|integer',
            'corp_id'               => 'nullable|integer',
            'school_id'             => 'nullable|integer',
            'realname'              => 'required|string',
            'english_name'          => 'nullable|string|between:2,64',
            'gender'                => 'required|boolean',
            'enabled'               => 'required|boolean',
            'email'                 => 'nullable|email|unique:users,email,' . $this->input('id') . ',id',
            'wechatid'              => 'nullable|string|unique:users,wechatid,' . $this->input('id') . ',id',
            'password'              => 'string|min:6|confirmed',
            'password_confirmation' => 'string|min:6',
            'mobile.*'              => ['required', new Mobiles()],
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            for ($i = 0; $i < count($input['mobile']); $i++) {
                if (($i == $defaultIndex)) {
                    $input['mobile'][$i]['isdefault'] = 1;
                } else {
                    $input['mobile'][$i]['isdefault'] = 0;
                }
                if (!isset($input['mobile'][$i]['enabled'])) {
                    $input['mobile'][$i]['enabled'] = 0;
                } else {
                    $input['mobile'][$i]['enabled'] = 1;
                }
            }
        }
        $this->replace($input);
        
    }
    
}
