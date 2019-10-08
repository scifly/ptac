<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SquadRequest
 * @package App\Http\Requests
 */
class SquadRequest extends FormRequest {
    
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
            'name'          => 'required|string|between:2,255|unique:classes,name,' .
                $this->input('id') . ',id,' .
                'grade_id,' . $this->input('grade_id'),
            'department_id' => 'required|integer',
            'grade_id'      => 'required|integer',
            'educator_ids'  => 'required|string',
            'tag_ids'       => 'nullable|array',
            'enabled'       => 'required|boolean',
        ];
        
    }
    
    /**
     * @return bool
     */
    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['educator_ids'] = join(',', $input['educator_ids'] ?? []);
        $input['department_id'] = $input['department_id'] ?? 0;
        $this->replace($input);
        
    }
    
}
