<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\{Email, Mobile};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class EducatorRequest
 * @package App\Http\Requests
 */
class EducatorRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        $rules = [
            'user.realname'              => 'required|string|between:2,255',
            'user.english_name'          => 'nullable|string|between:2,64',
            'user.gender'                => 'required|boolean',
            'user.group_id'              => 'required|integer',
            'user.username'              => 'required|string|between:2,255',
            'user.password'              => 'string|min:8|confirmed',
            'user.password_confirmation' => 'string|min:8',
            'user.telephone'             => 'nullable|string|between:2,64',
            'user.email'                 => ['nullable', 'email', new Email],
            'user.position'              => 'nullable|string|between:2,64',
            'user.enabled'               => 'required|boolean',
            'singular'                   => 'required|boolean',
            'school_id'                  => 'required|integer',
            'sms_quote'                  => 'nullable|integer',
            'enabled'                    => 'required|boolean',
            'mobile.*'                   => ['required', new Mobile],
            'selectedDepartments'        => 'required|array',
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
                $this->contactInput($this, 'educator')
            );
        }
        
    }
    
}
