<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use App\Models\Group;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

/**
 * Class OperatorRequest
 * @package App\Http\Requests
 */
class OperatorRequest extends FormRequest {
    
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
        
        # 批量操作（启用/禁用/删除）
        if (Request::has('ids')) {
            return [
                'ids' => 'required|array',
                'action' => [
                    'required', Rule::in(Constant::BATCH_OPERATIONS)
                ]
            ];
        }
        
        return [
            'username'              => 'required|string|between:6,255|unique:users,username,' .
                $this->input('id') . ',id',
            'group_id'              => 'required|integer',
            'corp_id'               => 'nullable|integer',
            'school_id'             => 'nullable|integer',
            'realname'              => 'required|string',
            'english_name'          => 'nullable|string|between:2,64',
            'gender'                => 'required|boolean',
            'user.email'            => ['nullable', 'email', new Email],
            'password'              => 'string|min:6|confirmed',
            'password_confirmation' => 'string|min:6',
            'mobile.*'              => ['required', new Mobile],
            'enabled'               => 'required|boolean',
            'synced'                => 'required|boolean',
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
        $input['avatar_url'] = '';
        $input['synced'] = 0;
        
        $this->replace($input);
        
    }
    
}
