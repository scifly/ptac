<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PassageLogRequest
 * @package App\Http\Requests
 */
class PassageLogRequest extends FormRequest {
    
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
        
        return [
            'school_id'       => 'required|integer',
            'user_id'         => 'required|integer',
            'passage_rule_id' => 'required|integer',
            'direction'       => 'required|boolean',
            'turnstile_id'    => 'required|integer',
            'door'            => 'required|integer|between:1,4',
            'clocked_at'      => 'required|date_format:Ymd H:i:s',
            'status'          => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
