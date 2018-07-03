<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class IconRequest
 * @package App\Http\Requests
 */
class IconRequest extends FormRequest {
    
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
            'name'         => 'required|string|max:60|unique:icons,name,' . $this->input('id') . ',id',
            'remark'       => 'required|string|max:255',
            'icon_type_id' => 'integer',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
}
