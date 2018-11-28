<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class DepartmentTypeRequest
 * @package App\Http\Requests
 */
class DepartmentTypeRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return Auth::user()->role() == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name'    => 'required|string|between:2,60|unique:department_types,name,' .
                $this->input('id') . ',id',
            'remark'  => 'nullable|string',
            'enabled' => 'required|boolean',
        ];
    }
    
}
