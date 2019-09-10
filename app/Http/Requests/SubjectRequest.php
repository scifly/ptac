<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SubjectRequest
 * @package App\Http\Requests
 */
class SubjectRequest extends FormRequest {
    
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
            'name'       => 'required|string|between:2,20|unique:subjects,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'max_score'  => 'required|numeric',
            'pass_score' => 'required|numeric',
            'school_id'  => 'required|integer',
            'grade_ids'  => 'required',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['grade_ids'])) {
            $input['grade_ids'] = join(',', $input['grade_ids']);
        }
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
