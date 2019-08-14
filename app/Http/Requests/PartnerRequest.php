<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Group;
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
            'group_id'              => 'required|integer',
            'username'              => 'required|string|between:6,255|unique:users,username,' .
                $this->input('id') . ',id',
            'password'              => 'required|string|min:6',
            'gender'                => 'required|boolean',
            'realname'              => 'required|string',
            'userid'                => 'required|string',
            'position'              => 'required|string',   # 接口类名
            'enabled'               => 'required|boolean',
            'synced'                => 'required|boolean',
            'subscribed'            => 'required|boolean',
            'school_id'             => 'required|integer'
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
    
        if (!$this->has('ids')) {
            $input = $this->all();
            $input['group_id'] = Group::whereName('api')->first()->id;
            # english_name - 接口密码明文
            $input['password'] = bcrypt($input['english_name']);
            $input['gender'] = 0;
            $input['userid'] = $input['username'] . uniqid();
            $input['synced'] = 0;
            $input['subscribed'] = 0;
            $this->replace($input);
        }
        
    }
    
}
