<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RoomTypeRequest
 * @package App\Http\Requests
 */
class RoomTypeRequest extends FormRequest {
    
    use ModelTrait;
    
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
            'name'             => 'required|string|max:255|unique:grades,name,' .
                $this->input('id') . ',id,' .
                'corp_id,' . $this->input('corp_id'),
            'corp_id'          => 'required|integer',
            'room_function_id' => 'required|integer',
            'remark'           => 'nullable|string|max:255',
            'enabled'          => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['corp_id'] = $this->corpId();
        $this->replace($input);
        
    }
    
}
