<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageTypeRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return Auth::user()->group->name == '运营';
        
    }
    
    public function rules() {
        
        return [
            'name'   => 'required|string|max:255|unique:message_types,name,'
                . $this->input('id') . ',id',
            'remark' => 'required|string|max:255',
        ];
        
    }
    
}
