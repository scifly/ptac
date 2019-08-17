<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CorpRequest
 * @package App\Http\Requests
 */
class CorpRequest extends FormRequest {

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
            'name'                => 'required|string|between:3,120|unique:corps,name,' .
                $this->input('id') . ',id,' .
                'company_id,' . $this->input('company_id'),
            'company_id'          => 'required|integer',
            'acronym'             => 'required|string|between:3,20|unique:corps,acronym,' .
                $this->input('id') . ',id',
            'department_id'       => 'nullable|integer',
            'menu_id'             => 'nullable|integer',
            'corpid'              => 'required|string|max:18',
            'departmentid'        => 'nullable|integer',
            'mchid'               => 'nullable|string|max:10',
            'apikey'              => 'nullable|string|max:32',
            'enabled'             => 'required|boolean'
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['company_id'] = $input['company_id']
            ?? Corp::whereDepartmentId($this->topDeptId($this->user()))->first()->company_id;

        $this->replace($input);
        
    }
    
}
