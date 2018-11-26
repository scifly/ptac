<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class IconTypeRequest
 * @package App\Http\Requests
 */
class IconTypeRequest extends FormRequest {
    
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
            'name'    => 'required|string|max:60|unique:icon_types,name,' . $this->input('id') . ',id',
            'remark'  => 'string|max:255',
            'enabled' => 'required|boolean',
        ];
        
    }
    
}
