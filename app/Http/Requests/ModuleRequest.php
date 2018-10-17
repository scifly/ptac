<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

/**
 * Class AttendanceMachineRequest
 * @package App\Http\Requests
 */
class ModuleRequest extends FormRequest {
    
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
            'name'      => 'required|string|between:2,60|unique:modules,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'remark'    => 'required|string|between:2,255',
            'school_id' => 'required|integer',
            'tab_id'    => 'nullable|integer',
            'uri'       => 'nullable|string',
            'order'     => 'required|integer',
            'media_id'  => 'required|integer',
            'isfree'    => 'required|boolean',
            'enabled'   => 'required|boolean',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $input = $this->all();
            $input['school_id'] = $this->schoolId();
            $this->replace($input);
        }
        
    }
    
}
