<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\School;
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
            'department_id'  => 'required|integer',
            'corp_id'        => 'required|integer',
            'menu_id'        => 'required|integer',
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
            if ($this->method() == 'POST') {
                # 保存 - store
                $input['department_id'] = $input['department_id'] ?? 0;
                $input['menu_id'] = $input['menu_id'] ?? 0;
            } else {
                # 更新 - update
                $school = School::find($this->input('id'));
                $input['department_id'] = $school->department_id;
                $input['menu_id'] = $school->menu_id;
            }
            $input['user_ids'] = join(',', $input['user_ids']);
            $this->replace($input);
        }
        
    }
    
}
