<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AlertTypeRequest extends FormRequest {
    
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
            'name'         => 'required|string|between:2,60|unique:alert_types,name,' .
                              $this->input('id') . ',id',
            'english_name' => 'required|string',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
}
