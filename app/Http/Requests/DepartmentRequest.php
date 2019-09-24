<?php
namespace App\Http\Requests;

use App\Models\Department;
use App\Models\DepartmentType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DepartmentRequest
 * @package App\Http\Requests
 */
class DepartmentRequest extends FormRequest {
    
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
            'name'               => 'required|string|between:2,255|unique:departments,name,' .
                $this->input('id') . ',id,' .
                'parent_id,' . $this->input('parent_id'),
            'parent_id'          => 'nullable|integer',
            'department_type_id' => 'required|integer',
            'remark'             => 'nullable|string|between:2,255',
            'order'              => 'nullable|integer',
            'tag_ids'            => 'nullable|array',
            'enabled'            => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        # 部门管理功能中只能添加类型为‘其他’的部门
        $input['department_type_id'] = DepartmentType::whereName('其他')->first()->id;
        $input['order'] = Department::all()->max('order') + 1;
        $this->replace($input);
        
    }
    
}
