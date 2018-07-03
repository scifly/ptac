<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        
        return Auth::user()->group->name == '运营';
        
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
            'department_id' => 'required|integer',
            'menu_id'       => 'required|integer',
            'remark'        => 'required|string',
            'enabled'       => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['department_id'] = $input['department_id'] ?? 0;
        $input['menu_id'] = $input['menu_id'] ?? 0;
        $this->replace($input);
        
    }
    
}
