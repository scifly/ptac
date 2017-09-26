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
            'operator.company_id' => 'required|integer',
//            'operator.user_id' => 'required|integer|unique:operators,user_id,' .
//                $this->input('Operator.id') . ',id',
            'operator.school_ids' => 'required|string',
            'user.group_id' => 'required|integer',
//            'user.username' => 'required|string|unique:users,username,' .
//                $this->input('User.id') . ',id',
            'user.realname' => 'required|string',
            'user.gender' => 'required|boolean',
            'user.enabled' => 'required|boolean',
            'user.email' => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
//            'user.password' => 'required|string|min:60',
            'mobile.*' => [
                'required',new Mobiles(),
            ],
        ];

    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['user']['enabled']) && $input['user']['enabled'] === 'on') {
            $input['user']['enabled'] = 1;
        }
        if (!isset($input['user']['enabled'])) {
            $input['user']['enabled'] = 0;
        }
        if (isset($input['operator']['school_ids'])) {
            $input['operator']['school_ids'] = implode(',', $input['operator']['school_ids']);
        }

        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);

            for ($i = 0; $i < count($input['mobile']); $i++) {
                if (($i == $defaultIndex)) {
                    $input['mobile'][$i]['isdefault'] = 1;
                }else{
                    $input['mobile'][$i]['isdefault'] = 0;
                }
                if ((!isset($mobile[$i]['enabled']))) {
                    $input['mobile'][$i]['enabled'] = 1;
                }else{
                    $input['mobile'][$i]['enabled'] = 0;
                }
            }

        }
        $this->replace($input);

    }
    
}
