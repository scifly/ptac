<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\Email;
use App\Rules\Mobile;
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
    
        $rules =  [
            'user.realname' => 'required|string',
            'user.gender'   => 'required|boolean',
            'user.group_id' => 'required|integer',
            'user.email'    => ['nullable', 'email', new Email],
            'mobile.*'      => ['required', new Mobile],
            'singular'      => 'required|boolean',
            'student_ids'   => 'required',
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
