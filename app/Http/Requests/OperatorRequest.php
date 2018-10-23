<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Group;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class OperatorRequest
 * @package App\Http\Requests
 */
class OperatorRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        $role = Auth::user()->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            switch ($role) {
                case '运营':
                    return true;
                case '企业':
                    return Group::find(Request::input('group_id'))->name != '运营';
                case '学校':
                    return Group::find(Request::input('group_id'))->name == '学校';
                default:
                    break;
            }
        }
        
        return false;
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        $rules = [
            'user.username'              => 'required|string|between:6,255|unique:users,username,' .
                $this->input('id') . ',id',
            'user.group_id'              => 'required|integer',
            'corp_id'                    => 'nullable|integer',
            'school_id'                  => 'nullable|integer',
            'user.realname'              => 'required|string',
            'user.english_name'          => 'nullable|string|between:2,64',
            'user.gender'                => 'required|boolean',
            'user.email'                 => ['nullable', 'email', new Email],
            'user.password'              => 'string|min:6|confirmed',
            'user.password_confirmation' => 'string|min:6',
            'mobile.*'                   => ['required', new Mobile],
            'user.enabled'               => 'required|boolean',
            'user.synced'                => 'required|boolean',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $this->replace(
                $this->contactInput($this, 'operator')
            );
        }
        
    }
    
}
