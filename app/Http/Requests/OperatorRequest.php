<?php
namespace App\Http\Requests;

use App\Helpers\{Constant, ModelTrait};
use App\Models\Group;
use App\Rules\{Email, Mobile};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\{Auth, Request};
use ReflectionException;

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
        
        $role = Auth::user()->role();
        if (in_array($role, Constant::SUPER_ROLES)) {
            $groupId = Request::input('user.group_id');
            switch ($role) {
                case '运营':
                    return true;
                case '企业':
                    return Group::find($groupId)->name != '运营';
                case '学校':
                    return Group::find($groupId)->name == '学校';
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
            'user.password'              => 'string|min:6|confirmed',
            'user.password_confirmation' => 'string|min:6',
            'user.group_id'              => 'required|integer',
            'user.realname'              => 'required|string|between:2,60',
            'user.english_name'          => 'nullable|string|between:2,64',
            'user.gender'                => 'required|boolean',
            'user.email'                 => ['nullable', 'email', new Email],
            'user.telephone'             => 'nullable|string|between:2,64',
            'user.enabled'               => 'required|boolean',
            'corp_id'                    => 'nullable|integer',
            'school_id'                  => 'nullable|integer',
            'mobile.*'                   => ['required', new Mobile],
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    /**
     * @throws ReflectionException
     */
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $this->replace(
                $this->contactInput($this, 'operator')
            );
        }
        
    }
    
}
