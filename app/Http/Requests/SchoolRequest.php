<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Corp;
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
                if ($this->has('id')) {
                    $school = School::find($this->input('id'));
                    $input['department_id'] = $school->department_id;
                    $input['menu_id'] = $school->menu_id;
                }
            }
            $input['school_type_id'] = $input['school_type_id'] ?? School::find($this->schoolId())->school_type_id;
            $input['user_ids'] = $input['user_ids'] ?? implode(',', $input['user_ids']);
            if (!isset($input['corp_id'])) {
                $departmentId = $this->topDeptId();
                $input['corp_id'] = $this->user()->role() == '企业'
                    ? Corp::whereDepartmentId($departmentId)->first()->id
                    : School::whereDepartmentId($departmentId)->first()->corp_id;
            }
            $this->replace($input);
        }
        
    }
    
}
