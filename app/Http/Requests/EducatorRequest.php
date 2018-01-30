<?php
namespace App\Http\Requests;

use App\Models\School;
use App\Rules\Mobiles;
use Illuminate\Foundation\Http\FormRequest;

class EducatorRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {

        $rules = [
            'educator.school_id' => 'required|integer',
            'user.group_id'      => 'required|integer',
            'user.realname'      => 'required|string',
            'user.gender'        => 'required|boolean',
            'user.enabled'       => 'required|boolean',
            'user.email'         => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
            'user.password'      => 'string|min:3|confirmed',
            'user.password_confirmation '      => 'string|min:3',
            'mobile.*'           => ['required', new Mobiles()],
            'selectedDepartments' => 'required|array'
        ];
        
        return $rules;
        
    }

    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['mobile'])) {
            $index = $input['mobile']['isdefault'];
            unset($input['mobile']['isdefault']);
            foreach ($input['mobile'] as $i => $m) {
//                $input['mobile'][$i]['user_id'] = $input['user_id'];
                $input['mobile'][$i]['user_id'] = isset($input['user_id']) ? $input['user_id'] : 0;

                if ($i == $index) {
                    $input['mobile'][$i]['isdefault'] = 1;
                } else {
                    $input['mobile'][$i]['isdefault'] = 0;
                }
                if (!isset($m['enabled'])) {
                    $input['mobile'][$i]['enabled'] = 0;
                } else {
                    $input['mobile'][$i]['enabled'] = 1;
                }
            }
        }
        $input['educator']['school_id'] = School::schoolId();
        
        $this->replace($input);
        
    }
    
}
