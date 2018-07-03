<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class SchoolTypeRequest
 * @package App\Http\Requests
 */
class SchoolTypeRequest extends FormRequest {
    
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
            'name'    => 'required|string|between:2,60|unique:school_types,name,' .
                $this->input('id') . ',id',
            'remark'  => 'string|between:2,255',
            'enabled' => 'required|boolean',
        ];
        
    }
    
}
