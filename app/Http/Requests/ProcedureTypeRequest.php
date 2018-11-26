<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ProcedureTypeRequest
 * @package App\Http\Requests
 */
class ProcedureTypeRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return User::find(Auth::id())->role() == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name'    => 'required|string|max:60|unique:procedure_types,name,' .
                $this->input('id') . ',id',
            'remark'  => 'required|string|max:255',
            'enabled' => 'required|boolean',
        ];
        
    }
    
}
