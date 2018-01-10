<?php
namespace App\Http\Requests;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
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
        if (isset($input['educator_ids'])) {
            $input['educator_ids'] = implode(',', $input['educator_ids']);
        }
        if (!isset($input['educator_ids'])) {
            $input['educator_ids'] = '0';
        }
        if (!isset($input['department_id'])) {
            $input['department_id'] = 0;
        }
        $input['school_id'] = School::schoolId();
        
        $this->replace($input);
        
    }
    
}
