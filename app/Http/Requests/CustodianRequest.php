<?php
namespace App\Http\Requests;

use App\Models\Group;
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
            'user.realname' => 'required|string',
            'user.gender'   => 'required|boolean',
            'user.group_id' => 'required|integer',
            'user.email'    => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
            'mobile.*'      => ['required', new Mobiles()],
            'student_ids'   => 'required',
        ];
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['mobile'])) {
            $defaultIndex = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            foreach ($input['mobile'] as $index => $mobile) {
                $input['mobile'][$index]['user_id'] = isset($input['user_id']) ? $input['user_id'] : 0;
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
        $input['user']['group_id'] = Group::whereName('监护人')->first()->id;
        $this->replace($input);
    }
    
}
