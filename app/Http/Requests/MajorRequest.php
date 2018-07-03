<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class MajorRequest
 * @package App\Http\Requests
 */
class MajorRequest extends FormRequest {
    
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
            'name'        => 'required|string|max:255',
            'remark'      => 'required|string|max:255|nullable',
            'school_id'   => 'required|integer',
            'enabled'     => 'required|boolean',
            'subject_ids' => 'required',
        ];
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
