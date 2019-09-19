<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EvaluateRequest
 * @package App\Http\Requests
 */
class EvaluateRequest extends FormRequest {
    
    use ModelTrait;
    
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
            'student_id'   => 'required|integer',
            'indicator_id' => 'required|integer',
            'semester_id'  => 'required|integer',
            'educator_id'  => 'nullable|integer',
            'amount'       => 'required|integer',
            'remark'       => 'nullable|string|max:255',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
}
