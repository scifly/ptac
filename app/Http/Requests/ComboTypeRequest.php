<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ComboTypeRequest
 * @package App\Http\Requests
 */
class ComboTypeRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'name'      => 'required|string|between:2,60|unique:combo_types,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('id'),
            'amount'    => 'required|integer',
            'discount'  => 'required|integer',
            'school_id' => 'required|integer',
            'months'    => 'required|integer',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
