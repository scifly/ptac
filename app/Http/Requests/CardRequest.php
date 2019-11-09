<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CardRequest
 * @package App\Http\Requests
 */
class CardRequest extends FormRequest {
    
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
            'sn'      => 'required|string|between:6,255|unique:cards,sn,' .
                $this->input('id') . ',id',
            'user_id' => 'required|integer|unique:cards,user_id,' .
                $this->input('id') . ',id',
            'status'  => 'required|boolean',
        ];
        
    }
    
}
