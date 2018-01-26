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
            'user.group_id'       => 'required|integer',
            'user.realname'       => 'required|string',
            'user.gender'         => 'required|boolean',
            'user.enabled'        => 'required|boolean',
            'user.email'          => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
            'user.password'      => 'string|min:3|confirmed',
            'user.password_confirmation '      => 'string|min:3',
            'mobile.*'            => [
                'required', new Mobiles(),
            ],
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['operator']['school_ids'])) {
            $input['operator']['school_ids'] = implode(',', $input['operator']['school_ids']);
        }
        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            for ($i = 0; $i < count($input['mobile']); $i++) {
                if (($i == $defaultIndex)) {
                    $input['mobile'][$i]['isdefault'] = 1;
                } else {
                    $input['mobile'][$i]['isdefault'] = 0;
                }
                if ((!isset($mobile[$i]['enabled']))) {
                    $input['mobile'][$i]['enabled'] = 1;
                } else {
                    $input['mobile'][$i]['enabled'] = 0;
                }
            }
        }
        echo '<pre>';
        print_r($input);exit;
        $this->replace($input);
        
    }
    
}
