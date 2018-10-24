<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

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
            'user.realname'  => 'required|string|between:2,255',
            'user.english_name'  => 'nullable|string|between:2,64',
            'user.gender'    => 'required|boolean',
            'user.group_id'  => 'required|integer',
            'user.telephone' => 'nullable|string|between:2,64',
            'user.email'     => ['nullable', 'email', new Email],
            'user.enabled'   => 'required|boolean',
            'class_id'       => 'required|integer',
            'card_number'    => 'required|alphanum|between:2,32|unique:students,card_number,' .
                $this->input('user_id') . ',user_id',
            'student_number' => 'required|alphanum|between:2,32|unique:students,student_number,' .
                $this->input('user_id') . ',user_id',
            'oncampus'       => 'required|boolean',
            'birthday'       => 'required',
            'remark'         => 'required|string',
            'enabled'        => 'required|boolean',
            'mobile.*'       => ['nullable', new Mobile],
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $this->replace(
                $this->contactInput($this, 'student')
            );
        }
        
    }
    
}
