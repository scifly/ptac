<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SchoolRequest
 * @package App\Http\Requests
 */
class SchoolRequest extends FormRequest {
    
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
            'name'           => 'required|string|between:6,255|unique:schools,name,' .
                $this->input('id') . ',id',
            'address'        => 'required|string|between:6,255',
            'signature'      => 'required|string|between:2,7',
            'corp_id'        => 'required|integer',
            'department_id'  => 'nullable|integer',
            'menu_id'        => 'nullable|integer',
            'school_type_id' => 'required|integer',
            'enabled'        => 'required|boolean',
            'app_id'         => 'nullable|integer',
            'user_ids'       => 'nullable|string'
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!$this->has('ids')) {
            $input = $this->all();
            $input['user_ids'] = join(',', $input['user_ids']);
            $this->replace($input);
        }
        
    }
    
}
