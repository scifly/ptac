<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventRequest
 * @package App\Http\Requests
 */
class EventRequest extends FormRequest {
    
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
            'title'    => 'required|string|between:1,40',
            'remark'   => 'required',
            'location' => 'required|string',
            'contact'  => 'required|string',
            'url'      => 'required|string',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['start'] = $input['start'] ?? "1970-01-01 00:00:00";
        $input['end'] = $input['end'] ?? "1970-01-01 00:00:00";
        $this->replace($input);
        
    }
    
}
