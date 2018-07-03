<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class MenuTypeRequest
 * @package App\Http\Requests
 */
class MenuTypeRequest extends FormRequest {
    
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
            'name'    => 'required|string|between:2,60|unique:menu_types,name,' .
                $this->input('id') . ',id',
            'remark'  => 'nullable|string',
            'enabled' => 'required|boolean',
        ];
        
    }
    
}
