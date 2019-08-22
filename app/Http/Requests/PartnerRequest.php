<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Group;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class OperatorRequest
 * @package App\Http\Requests
 */
class PartnerRequest extends FormRequest {
    
    use ModelTrait;
    
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
            # 合作伙伴全称
            'realname'  => 'required|string',
            # 接口用户名
            'username'  => 'required|string|between:6,255|unique:users,username,' .
                $this->input('id') . ',id',
            # 接口密码(明文)
            'secret'    => 'required|string|between:6,255',
            # 接口密码(密文)
            'password'  => 'required|string|min:6',
            # 接口类名
            'classname' => 'required|string',
            # 联系人
            'contact'   => 'nullable|string',
            'mobile'    => ['nullable', new Mobile],
            'email'     => ['nullable', new Email],
            'gender'    => 'required|boolean',
            'group_id'  => 'required|integer',
            'school_id' => 'required|integer',
            'enabled'   => 'required|boolean',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!$this->has('ids')) {
            $input = $this->all();
            $input['group_id'] = Group::whereName('api')->first()->id;
            # secret - 接口密码明文
            $input['password'] = bcrypt($input['secret']);
            $input['gender'] = 0;
            $this->replace($input);
        }
        
    }
    
}
