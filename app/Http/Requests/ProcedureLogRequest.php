<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProcedureLogRequest
 * @package App\Http\Requests
 */
class ProcedureLogRequest extends FormRequest {
    
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
            'procedure_id'  => 'required|integer',
            'initiator_msg' => 'required|string|max:255',
            'media_ids'     => 'array',
        ];
        
    }
    
}
