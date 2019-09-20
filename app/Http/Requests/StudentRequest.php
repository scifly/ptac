<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\{Email, Mobile};
use Exception;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StudentRequest
 * @package App\Http\Requests
 */
class StudentRequest extends FormRequest {
    
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
            'user.group_id'     => 'required|integer',
            'user.username'     => 'nullable|string|between:2,255',
            'user.gender'       => 'required|boolean',
            'user.realname'     => 'required|string|between:2,255',
            'user.userid'       => 'nullable|string|between:2,64',
            'user.english_name' => 'nullable|string|between:2,64',
            'user.telephone'    => 'nullable|string|between:2,64',
            'user.email'        => ['nullable', 'email', new Email],
            'user.mobile'       => ['nullable', new Mobile],
            'user.enabled'      => 'required|boolean',
            'class_id'          => 'required|integer',
            'sn'                => 'required|alphanum|between:2,32|unique:students,sn,' .
                $this->input('user_id') . ',user_id',
            'oncampus'          => 'required|boolean',
            'birthday'          => 'required',
            'enabled'           => 'required|boolean',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    /**
     * @throws Exception
     */
    protected function prepareForValidation() {
        
        $this->has('ids') ?: $this->replace($this->contactInput($this, 'student'));
        
    }
    
}
