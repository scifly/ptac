<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

/**
 * Class ScoreRequest
 * @package App\Http\Requests
 */
class ScoreRequest extends FormRequest {
    
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
        
        if (Request::has('ids')) {
            return [
                'ids' => 'required|array',
                'action' => [
                    'required', Rule::in(Constant::BATCH_OPERATIONS)
                ]
            ];
        }
        return [
            'student_id' => 'required|integer|unique:scores,student_id,' .
                $this->input('id') . ',id,' .
                'subject_id,' . $this->input('subject_id') . ',' .
                'exam_id,' . $this->input('exam_id'),
            'subject_id' => 'required|integer',
            'exam_id'    => 'required|integer',
            'score'      => 'required|numeric',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['class_rank'] = 0;
        $input['grade_rank'] = 0;
        $this->replace($input);
        
    }
    
}
