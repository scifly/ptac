<?php
namespace App\Http\Requests;

use App\Rules\Mobiles;
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
        $rules = [
//            'user.realname' => 'required|string|between:2,255|unique:users,realname,' .
//                $this->input('user_id') . ',id,'.
//                'gender,' . $this->input('user.gender') ,
            'user.realname' => 'required|string',
            'user.gender'   => 'required|boolean',
            'user.group_id' => 'required|integer',
            'user.email'    => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
//            'mobile.mobile' => 'required|string|unique:mobiles,mobile,'
//                . $this->input('user_id') . ',user_id',
            'mobile.*'      => [
                'required', new Mobiles(),
            ],
        ];

        return $rules;
//        $validateRules=[];
//        foreach ($input['mobile'] as $index => $mobile) {
//            $rule =[
//                'mobile.'.$index.'.mobile' => 'required|string|size:11|regex:/^1[34578][0-9]{9}$/|' .
//                    'unique:mobiles,mobile,' . $this->input('mobile.' . $index . '.id') . ',id',
//                'mobile.'.$index.'.isdefault' => 'required|boolean',
//                'mobile.'.$index.'.enabled' => 'required|boolean'
//            ];
//            $validateRules =array_merge($rules,$rule,$validateRules);
//            unset($rule);
//        }
//        return $validateRules;
    }

    protected function prepareForValidation() {

        $input = $this->all();
        if (isset($input['user']['enabled']) && $input['user']['enabled'] === 'on') {
            $input['user']['enabled'] = 1;
        }
        if (!isset($input['user']['enabled'])) {
            $input['user']['enabled'] = 0;
        }
        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            foreach ($input['mobile'] as $index => $mobile) {
                if ($index == $defaultIndex) {
                    $input['mobile'][$index]['isdefault'] = 1;
                } else {
                    $input['mobile'][$index]['isdefault'] = 0;
                }
                if (!isset($mobile['enabled'])) {
                    $input['mobile'][$index]['enabled'] = 0;
                } else {
                    $input['mobile'][$index]['enabled'] = 1;
                }
            }
        }
        $this->replace($input);
    }

}
