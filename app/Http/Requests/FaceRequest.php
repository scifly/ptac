<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FaceRequest
 * @package App\Http\Requests
 */
class FaceRequest extends FormRequest {
    
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
            'faceid'      => 'required|string|between:6,255|unique:faces,faceid,' .
                $this->input('id') . ',id',
            'user_id' => 'required|integer|unique:faces,user_id,' .
                $this->input('id') . ',id',
            'state'  => 'required|boolean'
        ];
        
    }
    
}
