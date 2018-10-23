<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

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
            'school_id'         => 'required|integer',
            'user.group_id'              => 'required|integer',
            'user.realname'              => 'required|string',
            'user.gender'                => 'required|boolean',
            'user.enabled'               => 'required|boolean',
            'user.email'                 => ['nullable', 'email', new Email],
            'user.password'              => 'string|min:8|confirmed',
            'user.password_confirmation' => 'string|min:8',
            'mobile.*'                   => ['required', new Mobile],
            'selectedDepartments'        => 'required|array',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $this->replace(
                $this->contactInput($this, 'educator')
            );
        }
        
    }
    
}
