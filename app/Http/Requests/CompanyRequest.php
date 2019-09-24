<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CompanyRequest
 * @package App\Http\Requests
 */
class CompanyRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return $this->user()->role() == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name'          => 'required|string|between:4,40|unique:companies,name,' .
                $this->input('id') . ',id',
            'department_id' => 'nullable|integer',
            'menu_id'       => 'nullable|integer',
            'remark'        => 'required|string',
            'enabled'       => 'required|boolean',
        ];
        
    }
    
}
