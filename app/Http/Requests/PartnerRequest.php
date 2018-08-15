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
        
        $rules = [
            'group_id'              => 'required|integer',
            'username'              => 'required|string|between:6,255|unique:users,username,' .
                $this->input('id') . ',id',
            'password'              => 'required|string|min:6',
            'gender'                => 'required|boolean',
            'realname'              => 'required|string',
            'userid'                => 'required|string',
            'enabled'               => 'required|boolean',
            'synced'                => 'required|boolean',
            'subscribed'            => 'required|boolean',
        ];
        $method = explode('/', Request::path())[1];
        if ($method == 'update' && !Request::route('id')) {
            $rules = [
                'ids' => 'required|array',
                'action' => [
                    'required', Rule::in(['enable', 'disable', 'delete'])
                ],
                'field' => 'required|string'
            ];
        }
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
    
        $method = explode('/', Request::path())[1];
        if (!($method == 'update' && !Request::route('id'))) {
            $input = $this->all();
            $input['group_id'] = Group::whereName('api')->first()->id;
            $input['password'] = bcrypt($input['english_name']);
            $input['gender'] = 0;
            $input['userid'] = $input['username'] . uniqid();
            $input['synced'] = 0;
            $input['subscribed'] = 0;
            $this->replace($input);
        }
        
    }
    
}
