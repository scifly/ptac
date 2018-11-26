<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class MessageTypeRequest
 * @package App\Http\Requests
 */
class MessageTypeRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return User::find(Auth::id())->role() == '运营';
        
    }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'name'   => 'required|string|max:255|unique:message_types,name,'
                . $this->input('id') . ',id',
            'remark' => 'required|string|max:255',
        ];
        
    }
    
}
