<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FlowTypeRequest
 * @package App\Http\Requests
 */
class FlowRequest extends FormRequest {
    
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
            'flow_type_id' => 'required|integer',
            'user_id'      => 'required|integer',
            'logs'         => 'required|string',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
}
