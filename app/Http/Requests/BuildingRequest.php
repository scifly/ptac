<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BuildingRequest
 * @package App\Http\Requests
 */
class BuildingRequest extends FormRequest {
    
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
            'name'      => 'required|string|max:255|unique:grades,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'school_id' => 'required|integer',
            'floors'    => 'required|integer',
            'remark'    => 'nullable|string|max:255',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
