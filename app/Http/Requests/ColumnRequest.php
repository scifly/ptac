<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ColumnRequest
 * @package App\Http\Requests
 */
class ColumnRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'name'     => 'required|string|max:255|unique:columns,name,' .
                $this->input('id') . ',id,' .
                $this->input('wap_id') . ',wap_id',
            'wap_id'   => 'required|integer',
            'media_id' => 'required|integer',
            'enabled'  => 'required|boolean',
        ];
        
    }
    
}
