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
class PartnerRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return Auth::user()->group->name == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'group_id'              => 'required|integer',
            'username'              => 'required|string|between:6,255|unique:users,username,' .
                $this->input('id') . ',id',
            'password'              => 'string|min:6|confirmed',
            'password_confirmation' => 'string|min:6',
            'gender'                => 'required|boolean',
            'realname'              => 'required|string',
            'userid'                => 'required|string',
            'enabled'               => 'required|boolean',
            'synced'                => 'required|boolean',
            'subscribed'            => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['group_id'] = Group::whereName('api')->first()->id;
        $input['gender'] = 0;
        $input['userid'] = $input['username'] . uniqid();
        $input['synced'] = 0;
        $input['subscribed'] = 0;
        
        $this->replace($input);
        
    }
    
}
