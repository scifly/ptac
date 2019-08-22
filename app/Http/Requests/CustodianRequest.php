<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\{Email, Mobile};
use Illuminate\Foundation\Http\FormRequest;
use ReflectionException;

/**
 * Class CustodianRequest
 * @package App\Http\Requests
 */
class CustodianRequest extends FormRequest {
    
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
            'user.realname'     => 'required|string|between:2,255',
            'user.gender'       => 'required|boolean',
            'user.mobile'       => [new Mobile, 'required_without:user.email'],
            'user.email'        => ['email', new Email, 'required_without:user.mobile'],
            'user.enabled'      => 'required|boolean',
            'singular'          => 'required|boolean',
            'enabled'           => 'required|boolean',
            'student_ids'       => 'required',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    /**
     * @throws ReflectionException
     */
    protected function prepareForValidation() {
        
        if (!$this->has('ids')) {
            $this->replace(
                $this->contactInput($this, 'custodian')
            );
        }
    }
    
}
