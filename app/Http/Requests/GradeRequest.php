<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GradeRequest
 * @package App\Http\Requests
 */
class GradeRequest extends FormRequest {
    
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
        return [
            'name'          => 'required|string|max:255|unique:grades,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'department_id' => 'required|integer',
            'school_id'     => 'required|integer',
            'educator_ids'  => 'required|string',
            'enabled'       => 'required|boolean',
        ];
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['educator_ids'] = isset($input['educator_ids'])
            ? implode(',', $input['educator_ids']) : '0';
        if (!isset($input['department_id'])) {
            $input['department_id'] = 0;
        }
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
