<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\{Email, Mobile};
use Exception;
use Illuminate\Foundation\Http\FormRequest;

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
            'user.gender'                => 'required|boolean',
            'user.group_id'              => 'required|integer',
            'user.username'              => 'required|string|between:2,255',
            'user.password'              => 'string|min:8|confirmed',
            'user.password_confirmation' => 'string|min:8',
            'user.mobile'                => [new Mobile, 'required_without:user.email'],
            'user.email'                 => ['email', new Email, 'required_without:user.mobile'],
            'user.enabled'               => 'required|boolean',
            
            'school_id'                  => 'required|integer',
            'singular'                   => 'required|boolean',
            'enabled'                    => 'required|boolean',
            'selectedDepartments'        => 'required|array',
            'tag_ids'                    => 'nullable|array'
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    /**
     * @throws Exception
     */
    protected function prepareForValidation() {
        
        if (!$this->has('ids')) {
            $this->replace(
                $this->contactInput($this, 'educator')
            );
        }
        
    }
    
}
