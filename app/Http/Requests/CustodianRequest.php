<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\{Email, Mobile};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

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
            'user.english_name' => 'nullable|string|between:2,64',
            'user.gender'       => 'required|boolean',
            'user.telephone'    => 'nullable|string|between:2,64',
            'user.email'        => ['nullable', 'email', new Email],
            'user.enabled'      => 'required|boolean',
            'singular'          => 'required|boolean',
            'enabled'           => 'required|boolean',
            'mobile.*'          => ['required', new Mobile],
            'student_ids'       => 'required',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $this->replace(
                $this->contactInput($this, 'custodian')
            );
        }
    }
    
}
