<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureLogRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'procedure_id'  => 'required|integer',
            'initiator_msg' => 'required|string|max:255',
            'media_ids'     => 'array',
        ];
        
    }
    
}
